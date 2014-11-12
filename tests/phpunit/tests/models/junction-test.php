<?php

class JunctionTest extends WP_UnitTestCase {

	private $blogIDs;
	private $postIDsPerBlog;
	private $currentJunction;

	public static function setUpBeforeClass() {


	}

	public function setUp() {
		parent::setUp();

		// Create multiple Blogs
		$this->blogIDs = $this->factory->blog->create_many( 3 );

		// Create multiple Junctions in each Blog
		foreach ( $this->blogIDs as $blogID ) {
			$this->postIDsPerBlog[ $blogID ] = null;
			switch_to_blog( $blogID );
			$postIDs                         = $this->factory->post->create_many( 3 );
			$this->postIDsPerBlog[ $blogID ] = $postIDs;

			$singleSite = new \MuNeCo\Model\Site( $blogID );
			$singleSite->setMunecostatus( true );
			restore_current_blog();
		}

		// Create Object
		$this->currentJunction = new \MuNeCo\Model\Junction( $this->blogIDs[0], $this->postIDsPerBlog[ $this->blogIDs[0] ] );
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->currentJunction );
	}

	public function test_GetJunction() {
		$theJunction = $this->currentJunction->getJunction();
		$this->assertEquals( 'post', $theJunction->posttype );
	}

	public function test_SetGetConnections() {
		$this->assertEmpty( $this->currentJunction->getConnections( true ) );
		$connectionsArray                      = array();
		$connectionsArray[ $this->blogIDs[0] ] = $this->postIDsPerBlog[ $this->blogIDs[0] ][0];
		$connectionsArray[ $this->blogIDs[1] ] = $this->postIDsPerBlog[ $this->blogIDs[1] ][0];
		$this->currentJunction->setConnections( $connectionsArray );
		$this->assertEquals( $connectionsArray, $this->currentJunction->getConnections( true ) );
	}
}

