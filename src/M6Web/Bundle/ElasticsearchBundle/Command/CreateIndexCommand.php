<?php

namespace M6Web\Bundle\ElasticsearchBundle\Command;

/**
 * Command to create indices
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class CreateIndexCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('m6web:elasticsearch:create_index')
            ->setDescription('Create an index using configuration')
            ->addArgument(
                'index',
                InputArgument::REQUIRED,
                'Index that should be created'
            )
            ->addArgument(
                'host',
                InputArgument::OPTIONAL,
                'Host where will be created the index (default if not set)',
                'default'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $host  = $input->getArgument('host');
        $index = $input->getArgument('index');
     
        $config = $this->getConfiguration($host, $index);
        
        $output->writeln(sprintf(
            'Creating "%s" index on "%s" host',
            $host,
            $index
        ));
        
        $client = $this->getContainer()
            ->get(sprintf('m6web_elasticsearch.client.%s', $host));
            
        $client->indices()->create($config);
    }
    
    private function getConfiguration($host, $index)
    {
        $parameterName = sprintf(
            'm6web_elasticsearch.index.%s.%s', 
            $host, 
            $index
        );
        
        if (false === $this->getContainer()->hasParameter($parameterName)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Index "%s" on host "%s" not found',
                    $index,
                    $host
                )
            );
        }
        
        return $this->getContainer()->getParameter($parameterName);
    }
}