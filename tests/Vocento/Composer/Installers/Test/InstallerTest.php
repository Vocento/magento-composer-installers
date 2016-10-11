<?php
/**
 * InstallerTest.php
 *
 * Ariel Ferrandini <arielferrandini@gmail.com>
 * 4/10/16
 */
namespace Vocento\Composer\Installers\Test;

use Composer\Composer;
use Composer\Config;
use Composer\Package\Package;
use Composer\Util\Filesystem;
use Vocento\Composer\Installers\Installer;

class InstallerTest extends TestCase
{
    private $composer;
    private $config;
    private $vendorDir;
    private $binDir;
    private $dm;
    private $repository;
    private $io;
    private $fs;

    /**
     * setUp
     *
     * @return void
     */
    public function setUp()
    {
        $this->fs = new Filesystem();

        $this->composer = new Composer();
        $this->config = new Config();
        $this->composer->setConfig($this->config);

        $this->vendorDir = realpath(sys_get_temp_dir()).DIRECTORY_SEPARATOR.'baton-test-vendor';
        $this->ensureDirectoryExistsAndClear($this->vendorDir);

        $this->binDir = realpath(sys_get_temp_dir()).DIRECTORY_SEPARATOR.'baton-test-bin';
        $this->ensureDirectoryExistsAndClear($this->binDir);

        $this->config->merge(
            array(
                'config' => array(
                    'vendor-dir' => $this->vendorDir,
                    'bin-dir' => $this->binDir,
                ),
            )
        );

        $this->dm = $this->getMockBuilder('Composer\Downloader\DownloadManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->composer->setDownloadManager($this->dm);

        $this->repository = $this->getMock('Composer\Repository\InstalledRepositoryInterface');
        $this->io = $this->getMock('Composer\IO\IOInterface');
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown()
    {
        $this->fs->removeDirectory($this->vendorDir);
        $this->fs->removeDirectory($this->binDir);
    }

    /**
     * testSupports
     *
     * @return void
     *
     * @dataProvider dataForTestSupport
     */
    public function testSupports($type, $expected)
    {
        $installer = new Installer($this->io, $this->composer);
        $this->assertSame($expected, $installer->supports($type), sprintf('Failed to show support for %s', $type));
    }

    /**
     * testInstallPath
     *
     * @dataProvider dataForTestInstallPath
     */
    public function testInstallPath($type, $path, $name, $version = '1.0.0')
    {
        $installer = new Installer($this->io, $this->composer);
        $package = new Package($name, $version, $version);

        $package->setType($type);
        $result = $installer->getInstallPath($package);
        $this->assertEquals($this->vendorDir.$path, $result);
    }

    /**
     * testInstallerClass
     *
     * @dataProvider dataTestInstallerClass
     */
    public function testInstallerClass($type, $class)
    {
        $installer = new Installer($this->io, $this->composer);
        $package = new Package('package-name', 'test-version', 'test-version');

        $package->setType($type);
        $packageInstaller = $installer->getPackageInstaller($package);

        $this->assertInstanceOf('Vocento\Composer\Installers\MagentoInstallerInterface', $packageInstaller);
        $this->assertInstanceOf($class, $packageInstaller);
    }

    /**
     * dataForTestSupport
     */
    public function dataForTestSupport()
    {
        return array(
            array('vocento-magento-core', true),
            array('vocento-magento-community', true),
            array('vocento-magento-statics', true),
            array('magento', false)
        );
    }

    /**
     * dataFormTestInstallPath
     */
    public function dataForTestInstallPath()
    {
        return array(
            array('magento-core', '/vocento/magento-core', 'vocento/magento-core')
        );
    }

    /**
     * dataTestInstallerClass
     */
    public function dataTestInstallerClass()
    {
        return array(
            array('vocento-magento-core', 'Vocento\Composer\Installer\MagentoCoreInstaller'),
            array('vocento-magento-community', 'Vocento\Composer\Installer\MagentoCommunityInstaller'),
            array('vocento-magento-statics', 'Vocento\Composer\Installer\MagentoStaticsInstaller'),
        );
    }
}
