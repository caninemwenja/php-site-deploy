<?php

require_once('functions.php');

class UpdateTest extends PHPUnit_Framework_TestCase
{
    public function testUrlRead()
    {
        $url = "http://localhost/test.txt";
        $control_data = @file_get_contents($url);
        $data = url_read($url);
        $this->assertEquals($control_data, $data, "Control data does not match fetched data");
    }

    /**
    * @depends testUrlRead
    */
    public function testRemoteCopy(){
        $url = "http://localhost/test.txt";
        $file = "test.txt";

        @unlink($file);

        $this->assertFalse(file_exists($file), "File already exists");

        $control_data = @file_get_contents($url);
        remote_copy($url, $file);

        $this->assertTrue(file_exists($file), "File was not created");
        $this->assertEquals($control_data, @file_get_contents($file), "File does not have the same content as original");

        // cleaning up
        @unlink($file);
    }

    // needs tests for extracting zip and tar
}
?>