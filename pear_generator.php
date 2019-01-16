<?php
/**
 * Copyright (c) 2019, Commify Ltd.
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of Commify nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Packaging
 * @package    Esendex
 * @author     Commify Support <support@esendex.com>
 * @copyright  2019 Commify Ltd.
 * @license    http://opensource.org/licenses/BSD-3-Clause  BSD 3-Clause
 * @link       https://github.com/esendex/esendex-php-sdk
 */
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
            '/test' => 'test',
            '/' => 'doc'
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
    'BSD-3-Clause',
    false,
    "LICENSE"
);

$package->addMaintainer(
    'lead',
    'support',
    'Commify Support',
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
