<?php
/**
 * Created by PhpStorm.
 * User: slav
 * Date: 10/02/15
 * Time: 9:51 AM
 */

class MinifyTest extends PHPUnit_Framework_TestCase {

	public $minify;

	public function setUp() {
		include_once('application/libraries/Minify.php');
	}

	// test init test
	public function testInit()
	{

		// Arrange
		$this->minify = new Minify();

		// Assert
		$this->assertTrue(is_object($this->minify), 'is object');
	}

	// check js compression
	public function testJsCompress()
	{
		$this->minify = new Minify();

		$this->minify->js_dir = 'assets/js';
		$this->minify->js(array('helpers.js'));

		$result = $this->minify->deploy_js(FALSE, 'ut.js');
		$this->assertTrue(is_string($result), 'deploy js with name');

		$this->assertEquals($this->minify->js_file, 'ut.min.js', 'output js file name');
	}

	// check js compression with closule compiler
	public function testJsCompressWithClosureCompiler()
	{
		$this->minify = new Minify();

		$this->minify->js_dir = 'assets/js';
		$this->minify->compression_engine['js'] = 'closurecompiler';
		$this->minify->js(array('helpers.js'));

		$result = $this->minify->deploy_js(TRUE, 'ut.js');
		$this->assertTrue(is_string($result), 'deploy js with closurecompiler');
		$this->assertEquals($this->minify->js_file, 'ut.min.js', 'output js file name');

		$file_content = file_get_contents($this->minify->assets_dir.DIRECTORY_SEPARATOR.$this->minify->js_file);
		$this->assertNotContains('Error', $file_content, 'output file has errors');
		$this->assertTrue( ! empty($file_content), 'output is not empty');
		
	}

	// test css compresion
	public function testCssCompress()
	{
		$this->minify = new Minify();

		$this->minify->css_dir = 'assets/css';
		$this->minify->css(array('style.css'));

		$result = $this->minify->deploy_css(TRUE, 'ut.css');
		$this->assertTrue(is_string($result), 'deploy css with name');

		$this->assertEquals($this->minify->css_file, 'ut.min.css', 'output css file name');
	}

	// test js with auto names
	public function testJsCompressWithAutoNames()
	{
		$this->minify = new Minify();

		$this->minify->auto_names = TRUE;
		$this->minify->js_dir = 'assets/js';
		$this->minify->js(array('helpers.js'));

		$result = $this->minify->deploy_js(FALSE);
		$this->assertTrue(is_string($result), 'deploy js with auto name');

		$this->assertEquals($this->minify->js_file, '91e30b9b77dc616476b94acf4dbb25c1.min.js', 'output js auto file name');
	}

	// test css with auto names
	public function testCssCompressWithAutoNames()
	{
		$this->minify = new Minify();

		$this->minify->auto_names = TRUE;
		$this->minify->css_dir = 'assets/css';
		$this->minify->css(array('style.css'));

		$result = $this->minify->deploy_css(TRUE);
		$this->assertTrue(is_string($result), 'deploy css with auto name');

		$this->assertEquals($this->minify->css_file, '72ac8bfd7cb9dd0f9df9ef4aafe0c714.min.css', 'output css auto file name');
	}

	// test js add
	public function testJsCompressWithAdd()
	{
		$this->minify = new Minify();

		$this->minify->js_dir = 'assets/js';
		$this->minify->add_js(array('helpers.js'))->add_js('jqModal.js');

		$result = $this->minify->deploy_js(FALSE);
		$this->assertTrue(is_string($result), 'deploy with add_js');

		$this->assertEquals($this->minify->js_file, 'scripts.min.js', 'output js default file name');
	}

	// tetst css with cadd
	public function testCssCompressWithAdd()
	{
		$this->minify = new Minify();

		$this->minify->css_dir = 'assets/css';
		$this->minify->add_css(array('style.css'))->add_css('browser-specific.css');

		$result = $this->minify->deploy_css(TRUE);
		$this->assertTrue(is_string($result), 'deploy with add_css');

		$this->assertEquals($this->minify->css_file, 'styles.min.css', 'output css default file name');
	}


	public function testJsCompressWithIndividualAutoName()
	{
		$this->minify = new Minify();

		$this->minify->js_dir = 'assets/js';
		$this->minify->js(array('helpers.js'));

		$result = $this->minify->deploy_js(FALSE, 'auto');
		$this->assertTrue(is_string($result), 'deploy js with individual auto name');

		$this->assertEquals($this->minify->js_file, '91e30b9b77dc616476b94acf4dbb25c1.min.js', 'output js auto file name');
	}

	public function testCssCompressWithIndividualAutoName()
	{
		$this->minify = new Minify();

		$this->minify->css_dir = 'assets/css';
		$this->minify->css(array('style.css'));

		$result = $this->minify->deploy_css(TRUE, 'auto');
		$this->assertTrue(is_string($result), 'deploy css with individual auto name');

		$this->assertEquals($this->minify->css_file, '72ac8bfd7cb9dd0f9df9ef4aafe0c714.min.css', 'output css auto file name');
	}

	// feature/custom_folders_for_compiled_css_and_js
	public function testCustomJsPathForAssets()
	{
		$this->minify = new Minify();

		$this->minify->assets_dir_js = 'assets/js';
		$this->minify->js_dir = 'assets/js';
		$this->minify->js(array('helpers.js'));

		$result = $this->minify->deploy_js(TRUE, 'auto');
		$this->assertEquals('<script type="text/javascript" src="assets/js/91e30b9b77dc616476b94acf4dbb25c1.min.js"></script>'.PHP_EOL, $result, 'deploy js with custom save path');
	}

	//
	public function testCustomCssPathForAssets()
	{
		$this->minify = new Minify();

		$this->minify->assets_dir_css = 'assets/css';
		$this->minify->css_dir        = 'assets/css';
		$this->minify->css(array('style.css'));

		$result = $this->minify->deploy_css(TRUE, 'auto');
		$this->assertEquals('<link href="assets/css/72ac8bfd7cb9dd0f9df9ef4aafe0c714.min.css" rel="stylesheet" type="text/css" />' . PHP_EOL, $result, 'deploy css with custom save path');
	}


	public function testCssCompressWithGroupNames()
	{
		$this->minify = new Minify();

		$this->minify->css_dir = 'assets/css';
		$this->minify->add_css(array('style.css'), 'sample1')->add_css('browser-specific.css', 'sample2');

		$result = $this->minify->deploy_css(TRUE, NULL, 'sample1');
		$this->assertTrue(is_string($result), 'deploy with group name: sample1');

		$this->assertEquals($this->minify->css_file, 'sample1_styles.min.css', 'output css default file name for group: sample1');

		$result = $this->minify->deploy_css(TRUE, NULL, 'sample2');
		$this->assertTrue(is_string($result), 'deploy with group name: sample2');

		$this->assertEquals($this->minify->css_file, 'sample2_styles.min.css', 'output css default file name for group: sample2');
	}

	public function testJsCompressWithGroupNames()
	{
		$this->minify = new Minify();

		$this->minify->css_dir = 'assets/js';
		$this->minify->add_js(array('helpers.js'), 'sample1')->add_js('jqModal.js', 'sample2');

		$result = $this->minify->deploy_js(TRUE, NULL, 'sample1');
		$this->assertTrue(is_string($result), 'deploy with group name: sample1');

		$this->assertEquals($this->minify->js_file, 'sample1_scripts.min.js', 'output js default file name for group: sample1');

		$result = $this->minify->deploy_js(TRUE, NULL, 'sample2');
		$this->assertTrue(is_string($result), 'deploy with group name: sample2');

		$this->assertEquals($this->minify->js_file, 'sample2_scripts.min.js', 'output js default file name for group: sample2');
	}
}