<?php
namespace Esendex;

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

require_once dirname(__FILE__) . '/vendor/autoload.php';

$apiVersion = Model\Api::getApiVersion();
$apiState = 'stable';
$releaseVersion = Model\Api::getVersion();
$releaseState = 'stable';

$description = <<<DESC
The Esendex PHP SDK for developing SMS capable applications using our REST API.

This library enables base functionality and examples of how to interact with the API.
DESC;

$releaseNotes = 'Initial release';

require_once 'PEAR/PackageFileManager2.php';
\PEAR::setErrorHandling(PEAR_ERROR_DIE);

$package = new \PEAR_PackageFileManager2();
$package->setReleaseVersion($releaseVersion);
$package->setReleaseStability($releaseState);
$package->setAPIVersion($apiVersion);
$package->setAPIStability($apiState);

$package->setOptions(
    array(
        'filelistgenerator' => 'file',
        'simpleoutput' => true,
        'baseinstalldir' => '/',
        'packagedirectory' => './',
        'clearcontents' => true,
        'dir_roles' => array(
            '/Esendex' => 'php',
            '/test' => 'test'
        ),
        'ignore' => array(
            'TestResult.xml',
            'build.xml',
            'composer.*',
            '*.phar',
            '*.tar.gz'
        )
    )
);

$package->setPackage('Esendex');
$package->setSummary('Esendex PHP REST SDK');
$package->setDescription($description);
$package->setNotes($releaseNotes);
$package->setChannel('esendex.github.com/pear');
$package->setPackageType('php');
$package->setLicense(
    'TODO',
    'http://example.com/licence'
);

$package->addMaintainer(
    'lead',
    'support',
    'Esendex Support',
    'support@esendex.com'
);

$package->setPhpDep('5.3.0');
$package->setPearInstallerDep('1.9.3');
$package->addPackageDepWithChannel('optional', 'PHPUnit', 'pear.phpunit.de');

$package->generateContents();
$package->addRelease();

if (isset($_GET['make'])
    || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')
) {
    $package->writePackageFile();
} else {
    $package->debugPackageFile();
}
