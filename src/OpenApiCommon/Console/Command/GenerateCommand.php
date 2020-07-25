<?php

namespace Jane\OpenApiCommon\Console\Command;

use Jane\JsonSchema\Console\Command\GenerateCommand as BaseGenerateCommand;
use Jane\JsonSchema\Console\Loader\ConfigLoaderInterface;
use Jane\JsonSchema\Console\Loader\SchemaLoaderInterface;
use Jane\JsonSchema\Printer;
use Jane\JsonSchema\Registry\RegistryInterface;
use Jane\OpenApiCommon\Console\Loader\OpenApiMatcher;
use Jane\OpenApiCommon\JaneOpenApi;
use Jane\OpenApiCommon\Registry\Registry;
use PhpParser\Node\Expr;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends BaseGenerateCommand
{
    /** @var OpenApiMatcher */
    private $matcher;

    public function __construct(ConfigLoaderInterface $configLoader, SchemaLoaderInterface $schemaLoader, OpenApiMatcher $matcher)
    {
        parent::__construct($configLoader, $schemaLoader);
        $this->matcher = $matcher;
    }

    public function configure()
    {
        $this->setName('generate');
        $this->setDescription('Generate an api client: class, normalizers and resources given a specific Json OpenApi file');
        $this->addOption('config-file', 'c', InputOption::VALUE_REQUIRED, 'File to use for Jane OpenAPI configuration', '.jane-openapi');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $this->configLoader->load($input->getOption('config-file'));
        $registries = $this->registries($options);

        /** @var Registry $registry */
        foreach ($registries as $registry) {
            $openApiClass = $registry->getOpenApiClass();
            /** @var JaneOpenApi $janeOpenApi */
            $janeOpenApi = $openApiClass::build($options);
            $fixerConfigFile = '';

            if (\array_key_exists('fixer-config-file', $options) && null !== $options['fixer-config-file']) {
                $fixerConfigFile = $options['fixer-config-file'];
            }

            $printer = new Printer(new Standard(), $fixerConfigFile);

            if (\array_key_exists('use-fixer', $options) && \is_bool($options['use-fixer'])) {
                $printer->setUseFixer($options['use-fixer']);
            }
            if (\array_key_exists('clean-generated', $options) && \is_bool($options['clean-generated'])) {
                $printer->setCleanGenerated($options['clean-generated']);
            }

            $janeOpenApi->generate($registry);
            $printer->output($registry);
        }

        return 0;
    }

    protected function newRegistry(string $schemaFile, array $options): RegistryInterface
    {
        $registry = new Registry();
        $registry->setOpenApiClass($this->matcher->match($schemaFile));
        $registry->setWhitelistedPaths($options['whitelisted-paths'] ?? []);

        $customQueryResolver = [];
        foreach ($options['custom-query-resolver'] ?? [] as $path => $methods) {
            if (!\array_key_exists($path, $customQueryResolver)) {
                $customQueryResolver[$path] = [];
            }

            foreach ($methods as $method => $parameters) {
                $method = mb_strtolower($method);
                if (!\array_key_exists($method, $customQueryResolver[$path])) {
                    $customQueryResolver[$path][$method] = [];
                }

                if ('__type' === $path) {
                    // here, variables has a different meaning:
                    // - path => '__type', meta-key to handle all types of ...
                    // - method => will contains the type of the query parameter where to apply this normalizer
                    // - parameters => will contains the file to apply
                    $customQueryResolver['__type'][$method] = $this->getCallbackAsExpr($parameters);
                    continue;
                }

                foreach ($parameters as $name => $file) {
                    if (!\array_key_exists($name, $customQueryResolver[$path][$method])) {
                        $customQueryResolver[$path][$method][$name] = [];
                    }

                    $customQueryResolver[$path][$method][$name] = $this->getCallbackAsExpr($file);
                }
            }
        }
        $registry->setCustomQueryResolver($customQueryResolver);

        return $registry;
    }

    private function getCallbackAsExpr($file): Expr
    {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $code = file_get_contents($file);
        $stmts = $parser->parse($code);

        return $stmts[0]->expr;
    }
}
