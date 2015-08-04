<?php

namespace BVM\SonataAdminTestsGeneratorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use BHW\Bundle\CoreBundle\Generator\AdminTestsGenerator;
use Doctrine\Common\Annotations\AnnotationReader;


class GenerateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('bvm:admin:generate-tests')
        ->setDescription('tests')
        ->addArgument(
            'admin_code',
            InputArgument::REQUIRED,
            'Admin Code'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $admin_code = $input->getArgument('admin_code');

        $admin 	= $this->getContainer()->get($admin_code);

        $skeletonDirectory = __DIR__ . '/../Resources/skeleton';

        $bundle_path =  $this->getBundlePathFromClass($admin->getClass());

        $generator = new AdminTestsGenerator();

        $generator->setSkeletonDirs($skeletonDirectory);

        dump($generator->generate($admin, $bundle_path));

    }


    /**
    * @see Sonata\AdminBundle\Command\GenerateAdminCommand
    * @param string $class
    *
    * @return string|null
    *
    * @throws \InvalidArgumentException
    */
    private function getBundleNameFromClass($class)
    {
        $application = $this->getApplication();

        foreach ($application->getKernel()->getBundles() as $bundle) {
            if (strpos($class, $bundle->getNamespace() . '\\') === 0) {
                return $bundle->getName();
            };
        }

        return null;
    }

    /**
    * @see Sonata\AdminBundle\Command\GenerateAdminComman
    * @param string $class
    *
    * @return string|null
    *
    * @throws \InvalidArgumentException
    */
    private function getBundlePathFromClass($class)
    {
        $application = $this->getApplication();
        /* @var $application Application */

        foreach ($application->getKernel()->getBundles() as $bundle) {
            if (strpos($class, $bundle->getNamespace() . '\\') === 0) {
                return $bundle->getPath();
            };
        }

        return null;
    }

    /**
     * [getRepositoryFromAdmin description]
     * @param  object $admin 
     * @return string        [description]
     */
    private function getRepositoryFromAdmin($admin)
    {
        $class = $admin->getClass();

        $bundle = $this->getBundleNameFromClass($class);
        
        $chunks = explode('\\', $class);

        $name = sprintf('%s:%s', $bundle, ucfirst(array_pop($chunks)));

        return $name;
    }

}