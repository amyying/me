<?php

class TestController extends BaseController {
    // public function getUploads() {
        
    //     // echo WEBROOT;
    //     return View::make('test.uploads');
    // }

    public function anyUploads() {
        if ($_POST) {
            $uploaddir = 'uploads/';
            $uploadfile = $uploaddir . basename($_FILES['input_uploadFile']['name']);
            echo $uploadfile;
            print_r($_FILES);

            echo '<pre>';
            if (move_uploaded_file($_FILES['input_uploadFile']['tmp_name'], $uploadfile)) {
                echo "File is valid, and was successfully uploaded.\n";
            } else {
                echo "Possible file upload attack!\n";
            }

            echo 'Here is some more debugging info:';
            print_r($_FILES);

            print "</pre>";
        }
        return View::make('test.uploads');

    }

    public function index() {
        echo "string";
        exit;
    }

    // public function show() {

    // }
}