<?php

require_once 'common.php';

function doBootstrap() {
		
	$errors = array();
	# need tmp_name -a temporary name create for the file and stored inside apache temporary folder- for proper read address
	$zip_file = $_FILES["bootstrap-file"]["tmp_name"];

	# Get temp dir on system for uploading
    $temp_dir = sys_get_temp_dir();

    $lines_processed = 0;
    
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

                // var_dump(fgetcsv($student));

                $bidDAO= new BidDAO();//has fk dependency on course, section, student
                $bidDAO->deleteAll();

                $course_completedDAO= new CourseCompletedDAO();//has fk dependency on course, student
                $course_completedDAO->deleteAll();

                $prerequisiteDAO= new PrerequisiteDAO();//has fk dependency on course
                $prerequisiteDAO->deleteAll();

                $sectionDAO= new SectionDAO();//has fk dependency on course
                $sectionDAO->deleteAll();

                $studentDAO= new StudentDAO();//no depdenency
                $studentDAO->deleteAll();

                $courseDAO= new CourseDAO();//no dependency
                $courseDAO->deleteAll();


                // read each line from csv
                //skip header
                $fields= fgetcsv($student);
                $filename = 'student.csv';
                $row_num = 1;
                $student_success = 0;
                // var_dump($fields);
   
                while ( ($student_arr=fgetcsv($student) )  !== false){
                    // var_dump($student_arr);
                    $student_arr = array_map('trim', $student_arr);//trims all cols in row
                    $row_num++;
                    $row_errors = [];
                    $skip_line = FALSE;
                    for ($i=0; $i<sizeof($student_arr); $i++){
                        if ($student_arr[$i] === ''){
                            $skip_line = TRUE;
                            $row_errors[] = "blank {$fields[$i]}";
                        }
                    }
                    if ($skip_line==FALSE){
                        //enters this if no empty values in row
                        $studentObj= new Student($student_arr[0],$student_arr[1],$student_arr[2],$student_arr[3],$student_arr[4]);
                        $row_errors = $studentDAO->add($studentObj);
                    }
                    if (!empty($row_errors)){
                        $errors[] = [$filename, $row_num, $row_errors];
                    }
                    else{
                        $student_success++;
                    }
                }
                fclose($student);
                unlink($student_path);
                
                $course_arr=fgetcsv($course);
                while ( ($course_arr=fgetcsv($course) )  !== false){
                    $courseObj= new Course($course_arr[0],$course_arr[1],$course_arr[2],$course_arr[3],$course_arr[4],$course_arr[5],$course_arr[6]);
                    $courseDAO->add($courseObj);
                    $lines_processed++;
                }
                fclose($course);
                unlink($course_path);
                

                $section_arr=fgetcsv($section);
                while ( ($section_arr=fgetcsv($section) )  !== false){
                    $sectionObj= new Section($section_arr[0],$section_arr[1],$section_arr[2],$section_arr[3],$section_arr[4],$section_arr[5],$section_arr[6],$section_arr[7]);
                    $sectionDAO->add($sectionObj);
                    $lines_processed++;
                }
                fclose($section);
                unlink($section_path);
                

                $prerequisite_arr=fgetcsv($prerequisite);
                while ( ($prerequisite_arr=fgetcsv($prerequisite) )  !== false){
                    $prerequisiteObj= new Prerequisite($prerequisite_arr[0],$prerequisite_arr[1]);
                    $prerequisiteDAO->add($prerequisiteObj);
                    $lines_processed++;
                }
                fclose($prerequisite);
                unlink($prerequisite_path);
                

                $course_completed_arr=fgetcsv($course_completed);
                while ( ($course_completed_arr=fgetcsv($course_completed) )  !== false){
                    $course_completedObj= new CourseCompleted($course_completed_arr[0],$course_completed_arr[1]);
                    $course_completedDAO->add($course_completedObj);
                    $lines_processed++;
                }
                fclose($course_completed);
                unlink($course_completed_path);
                

                $bid_arr=fgetcsv($bid);
                while ( ($bid_arr=fgetcsv($bid) )  !== false){
                    $bidObj= new Bid($bid_arr[0],$bid_arr[1],$bid_arr[2],$bid_arr[3]);
                    $bidDAO->add($bidObj);
                    $lines_processed++;
                }
                fclose($bid);
				unlink($bid_path);
            }


            }
        }
        var_dump($errors);
        echo $student_success;
    }





?>