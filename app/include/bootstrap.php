<?php

require_once 'common.php';

function doBootstrap() {
		
	$errors = array();
	# need tmp_name -a temporary name create for the file and stored inside apache temporary folder- for proper read address
	$zip_file = $_FILES["bootstrap-file"]["tmp_name"];

	# Get temp dir on system for uploading
    $temp_dir = sys_get_temp_dir();
    
    if ($_FILES["bootstrap-file"]["size"] <= 0)
        $errors[] = "input files not found";
        #checks if zip file is empty

	else {
		
		$zip = new ZipArchive;
		$res = $zip->open($zip_file);

		if ($res === TRUE) {
			$zip->extractTo($temp_dir);
            $zip->close();
            
            #sets path for the csv files
            $student_path = "$temp_dir/student.csv";
            $course_path = "$temp_dir/course.csv";
            $section_path = "$temp_dir/section.csv";
            $prerequisite_path = "$temp_dir/prerequisite.csv";
            $course_completed_path = "$temp_dir/course_completed.csv";
            $bid_path = "$temp_dir/bid.csv";



            $student = @fopen($student_path, "r");
            $course = @fopen($course_path, "r");
            $section = @fopen($section_path, "r");
            $prerequisite = @fopen($prerequisite_path, "r");
            $course_completed = @fopen($course_completed_path, "r");
            $bid= @fopen($bid_path, "r");

            if(empty($student) || empty($course) || empty($section) || empty($prerequisite) || empty($course_completed) || empty($bid)) {
                $errors[] = "input files not found";
				if (!empty($student)){
					fclose($student);
					@unlink($student_path); //delete the file from your computer 
				} 
				
				if (!empty($course)) {
					fclose($course);
					@unlink($course_path);
				}
				
				if (!empty($section)) {
					fclose($section);
					@unlink($section_path);
                }
                if (!empty($prerequisite)){
					fclose($prerequisite);
					@unlink($prerequisite_path); 
				} 
				
				if (!empty($course_completed)) {
					fclose($course_completed);
					@unlink($course_completed_path);
				}
				
				if (!empty($bid)) {
					fclose($bid);
					@unlink($bid_path);
				}
				
				
            }
            else{
                $studentDAO= new StudentDAO();
                $studentDAO->removeAll();
                
                $courseDAO= new CourseDAO();
                $courseDAO->removeAll();
                
                $sectionDAO= new SectionDAO();
                $studentDAO->removeAll();
                
                $prerequisiteDAO= new PrerequisiteDAO();
                $prerequisiteDAO->removeAll();
                
                $courseCompletedDAO= new CourseCompletedDAO();
                $courseCompletedDAO->removeAll();
                
                $bidDAO= new BidDAO();
                $bidDAO->removeAll();
                


                // read each line from csv
                //skip header
                $student_arr= fgetcsv($student);

                

                while ( ($student_arr=fgetcsv($student) )  !== false){
                    $studentObj= new Student($student_arr[0],$student_arr[1],$student_arr[2],$student_arr[3],$student_arr[4]);
                    $studentDAO->add($studentObj);
                }
                fclose($student);
                unlink($student_path);
                
                $course_arr=fgetcsv($course);
                while ( ($course_arr=fgetcsv($course) )  !== false){
                    $courseObj= new Course($course_arr[0],$course_arr[1],$course_arr[2],$course_arr[3],$course_arr[4],$course_arr[5],$course_arr[6]);
                    $courseDAO->add($courseObj);
                }
                fclose($course);
                unlink($course_path);
                

                $section_arr=fgetcsv($section);
                while ( ($section_arr=fgetcsv($section) )  !== false){
                    $sectionObj= new Section($section_arr[0],$section_arr[1],$section_arr[2],$section_arr[3],$section_arr[4],$section_arr[5],$section_arr[6],$section_arr[7]);
                    $sectionDAO->add($sectionObj);
                }
                fclose($section);
                unlink($section_path);
                

                $prerequisite_arr=fgetcsv($prerequisite);
                while ( ($prerequisite_arr=fgetcsv($prerequisite) )  !== false){
                    $prerequisiteObj= new Prerequisite($prerequisite_arr[0],$prerequisite_arr[1]);
                    $prerequisiteDAO->add($prerequisiteObj);
                }
                fclose($prerequisite);
                unlink($prerequisite_path);
                

                $courseCompleted_arr=fgetcsv($courseCompleted);
                while ( ($courseCompleted_arr=fgetcsv($courseCompleted) )  !== false){
                    $courseCompletedObj= new CourseCompleted($courseCompleted_arr[0],$courseCompleted_arr[1]);
                    $courseCompletedDAO->add($courseCompletedObj);
                }
                fclose($courseCompleted);
                unlink($courseCompleted_path);
                

                $bid_arr=fgetcsv($bid);
                while ( ($bid_arr=fgetcsv($bid) )  !== false){
                    $bidObj= new Bid($bid_arr[0],$bid_arr[1],$bid_arr[2],$bid_arr[3]);
                    $bidDAO->add($bidObj);
                }
                fclose($bid);
				unlink($bid_path);
            }


            }





?>