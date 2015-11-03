<?php
 $arr = array();
  $dir = new DirectoryIterator('batch/');
   foreach ($dir as $fileinfo) {
        if (!$fileinfo->isDot()) {
            //var_dump($fileinfo->getFilename());
             if ($dh = opendir( 'batch/'.$fileinfo->getFilename())) {

                $tmp = array();
                while (($file = readdir($dh)) !== false) {

                    if($file == '.' || $file==".." || $file == ".DS_Store") continue;
                    
                    //echo $dir;
                    //echo ($file);
                    //echo "process..<br />";
                    process($dir , $file);

                    //echo "<br />";
                   //echo "<br />";
                   
                  $tmp[]  = array('filename'=>$file,'status' => 'active' );
                    
                    //echo $file;
                    //Do your sql insert - the filename is in the $file variable
                }
               // array_push($arr , array($dir=>$tmp));
                 $arr["'".$dir."'"] = $tmp;

                closedir($dh);
            }
        }
    }

    echo json_encode($arr);
    //var_dump($dir)


    function process($category , $newname)
    {

        //echo $category;
        //echo $newname;
        //$newname = $_POST['filename'];
        //$category = $_POST['category'];
        $max_size = 800; //max image size in Pixels
        $max_size_thumb = 500;
        $destination_folder = $category;
        //echo $destination_folder;
        $watermark_png_file = 'watermark.png'; //path to watermark image
        
        $image_name = $newname; //file name
        //$image_size = $_FILES['file']['size']; //file size
        $image_temp = 'batch/'.$category.'/'.$newname; //file temp
        $image_type = 'image/jpeg'; //file type

        switch(strtolower($image_type)){ //determine uploaded image type 
                //Create new image from file
                case 'image/png': 
                    $image_resource =  imagecreatefrompng($image_temp);
                    break;
                case 'image/gif':
                    $image_resource =  imagecreatefromgif($image_temp);
                    break;          
                case 'image/jpeg': case 'image/pjpeg':
                    $image_resource = imagecreatefromjpeg($image_temp);
                    break;
                default:
                    $image_resource = false;
            }
        
        if($image_resource){
            //Copy and resize part of an image with resampling
            list($img_width, $img_height) = getimagesize($image_temp);
            
            //Construct a proportional size of new image
            $image_scale        = min($max_size / $img_width, $max_size / $img_height); 
            $new_image_width    = ceil($image_scale * $img_width);
            $new_image_height   = ceil($image_scale * $img_height);
            $new_canvas         = imagecreatetruecolor($new_image_width , $new_image_height);

            $image_scale_thumb        = min($max_size_thumb / $img_width, $max_size_thumb / $img_height); 
            $new_image_width_thumb    = ceil($image_scale_thumb * $img_width);
            $new_image_height_thumb   = ceil($image_scale_thumb * $img_height);
            $new_canvas_thumb         = imagecreatetruecolor($new_image_width_thumb , $new_image_height_thumb);

            //Resize image with new height and width
            if(imagecopyresampled($new_canvas, $image_resource , 0, 0, 0, 0, $new_image_width, $new_image_height, $img_width, $img_height))
            {
                
                if(!is_dir($destination_folder)){ 
                    mkdir($destination_folder);//create dir if it doesn't exist

                }

                 if(!is_dir($destination_folder.'/thumb')){ 
                    mkdir($destination_folder.'/thumb');//create dir if it doesn't exist

                }

                
                //calculate center position of watermark image
                $watermark_left = ($new_image_width/2)-(640/2); //watermark left
                $watermark_bottom = ($new_image_height/2)-(545/2); //watermark bottom
                $watermark_left = 10;
                $watermark_bottom = 10;

                $watermark = imagecreatefrompng($watermark_png_file); //watermark image

                //use PHP imagecopy() to merge two images.
                //imagecopymerge($new_canvas, $watermark, $watermark_left, $watermark_bottom, 0, 0, 150, 150, 30); //merge image

                
                //Or Save image to the folder
               // imagejpeg($new_canvas, $destination_folder.'/thumb/'.$newname , 90); 
                imagejpeg($new_canvas, $destination_folder.'/'.$newname , 90);
                
                //free up memory
                imagedestroy($new_canvas); 
               // imagedestroy($image_resource);
                //die();
            }

            if(imagecopyresampled($new_canvas_thumb, $image_resource , 0, 0, 0, 0, $new_image_width_thumb, $new_image_height_thumb , $img_width, $img_height))
            {

                 if(!is_dir($destination_folder.'/thumb')){ 
                    mkdir($destination_folder.'/thumb');//create dir if it doesn't exist

                }

                
                //calculate center position of watermark image
                //$watermark_left = ($new_image_width/2)-(640/2); //watermark left
                //$watermark_bottom = ($new_image_height/2)-(545/2); //watermark bottom
                $watermark_left = 10;
                $watermark_bottom = 10;

                $watermark = imagecreatefrompng($watermark_png_file); //watermark image

                
                //use PHP imagecopy() to merge two images.
                //imagecopymerge($new_canvas, $watermark, $watermark_left, $watermark_bottom, 0, 0, 15, 15, 30); //merge image
                //Or Save image to the folder
                imagejpeg($new_canvas_thumb, $destination_folder.'/thumb/'.$newname , 90); 
                
                //free up memory
                imagedestroy($new_canvas_thumb); 
                //die();
            }

            imagedestroy($image_resource);
                
        }
    }
?>