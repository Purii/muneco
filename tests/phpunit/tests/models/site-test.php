<?php

class SiteTest extends WP_UnitTestCase {

	private $blogIDs;

	public function setUp() {
		parent::setUp();
		$this->blogIDs = $this->factory->blog->create_many( 1 );
	}

	public function test_SetGetMunecostatus() {
		$singleSite = new \MuNeCo\Model\Site( $this->blogIDs[0] );
		$this->assertFalse( $singleSite->getMunecostatus() );
		$singleSite->setMunecostatus( true );
		$this->assertTrue( $singleSite->getMunecostatus() );
	}

	public function test_DeleteMunecostatus() {
		$singleSite = new \MuNeCo\Model\Site( $this->blogIDs[0] );
		$singleSite->setMunecostatus( true );
		$singleSite->deleteMunecostatus();
		$this->assertFalse( $singleSite->getMunecostatus() );
	}

	public function test_SetGetLanguagecode() {
		$singleSite = new \MuNeCo\Model\Site( $this->blogIDs[0] );
		$this->assertEquals( 'en-US', $singleSite->getLanguagecode() );
		$singleSite->setLanguagecode( 'de-DE' );
		$this->assertEquals( 'de-DE', $singleSite->getLanguagecode() );
	}

	public function test_DeleteLanguagecode() {
		$singleSite = new \MuNeCo\Model\Site( $this->blogIDs[0] );
		$singleSite->setLanguagecode( 'de-DE' );
		$singleSite->deleteLanguagecode();
		$this->assertEquals( 'en-US', $singleSite->getLanguagecode() );
	}

	public function tearDown() {
		parent::tearDown();
	}
}