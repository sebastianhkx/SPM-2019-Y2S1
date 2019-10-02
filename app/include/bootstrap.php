<?php

require_once 'common.php';

function doBootstrap() {
		
	$errors = array();
	# need tmp_name -a temporary name create for the file and stored inside apache temporary folder- for proper read address
	$zip_file = $_FILES["bootstrap-file"]["tmp_name"];

	# Get temp dir on system for uploading
    $temp_dir = sys_get_temp_dir();

    $student_success = 0;
    $course_success = 0;
    $section_success = 0;
    $prerequisite_success = 0;
    $course_completed_success = 0;
    $bid_success = 0;
    
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
					@unlink($student_path); //delete the file from your computer temp folder
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

                //todo delete all for new tables

                $resultDAO = new ResultDAO();
                $resultDAO->deleteAll();

                $courseEnrolledDAO = new CourseEnrolledDAO();
                $courseEnrolledDAO->deleteAll();


                // read each line from csv
                //skip header
                $fields= fgetcsv($student);
                $filename = 'student.csv';
                $row_num = 1;
                // var_dump($student);

                //processes student.csv
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
                        $studentObj = new Student($student_arr[0],$student_arr[1],$student_arr[2],$student_arr[3],$student_arr[4]);
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
                
                //processes course csv
                $fields= fgetcsv($course);
                $filename = 'course.csv';
                $row_num = 1;
                while ( ($course_arr=fgetcsv($course) )  !== false){
                    $course_arr= array_map('trim',$course_arr);
                    $row_num++;
                    $row_errors=[];
                    $skip_line= FALSE;
                    for ($i=0;$i<sizeof($course_arr);$i++){
                        if(($course_arr[$i]) === ''){
                            $skip_line=TRUE;
                            $row_errors[]="blank {$fields[$i]}";
                        }
                    }
                    if($skip_line==FALSE){
                        $courseObj = new Course($course_arr[0],$course_arr[1],$course_arr[2],$course_arr[3],$course_arr[4],$course_arr[5],$course_arr[6]);
                        $row_errors = $courseDAO->add($courseObj);
                    }
                    if(!empty($row_errors)){
                        $errors[]=[$filename, $row_num, $row_errors];
                    }
                    else{
                        $course_success++;
                    }
                }
                fclose($course);
                unlink($course_path);
                
                //processess section.csv
                $section_arr=fgetcsv($section);
                $filename = 'section.csv';
                $row_num = 1;
                while ( ($section_arr=fgetcsv($section) )  !== false){
                    $section_arr= array_map('trim',$section_arr);
                    $row_num++;
                    $row_errors=[];
                    $skip_line= FALSE;
                    for ($i=0;$i<sizeof($section_arr);$i++){
                        if(($section_arr[$i]) === ''){
                            $skip_line=TRUE;
                            $row_errors[]="blank {$fields[$i]}";
                        }
                    }
                    if($skip_line==FALSE){
                        $sectionObj = new Section($section_arr[0],$section_arr[1],$section_arr[2],$section_arr[3],$section_arr[4],$section_arr[5],$section_arr[6],$section_arr[7]);
                        $row_errors = $sectionDAO->add($sectionObj);
                    }
                    if(!empty($row_errors)){
                        $errors[]=[$filename, $row_num, $row_errors];
                    }
                    else{
                        $section_success++;
                    }
                }
               
                fclose($section);
                unlink($section_path);

                //processes prerequisite.csv
                $fields= fgetcsv($prerequisite);
                $filename = 'prerequisite.csv';
                $row_num = 1;


                while ( ($prerequisite_arr=fgetcsv($prerequisite) )  !== false){
                    $prerequisite_arr = array_map('trim', $prerequisite_arr);//trims all cols in row
                    $row_num++;
                    $row_errors = [];
                    $skip_line = FALSE;
                    for ($i=0; $i<sizeof($prerequisite_arr); $i++){//this loop checks for empty cols
                        if ($prerequisite_arr[$i] === ''){
                            $skip_line = TRUE;
                            $row_errors[] = "blank {$fields[$i]}";
                        }
                    }
                    if ($skip_line == FALSE){
                        $prerequisiteObj= new Prerequisite($prerequisite_arr[0],$prerequisite_arr[1]);
                        $row_errors = $prerequisiteDAO->add($prerequisiteObj);
                    }
                    if (!empty($row_errors)){
                        $errors[] = [$filename, $row_num, $row_errors];
                    }
                    else{
                        $prerequisite_success++;
                    }
                }
                fclose($prerequisite);
                unlink($prerequisite_path);
                
                //processes course_completed.csv
                //#does data validation for userid and course
                $fields=fgetcsv($course_completed);//gets rid of headers for course_completed
                $filename = "course_completed.csv";
                $row_num = 1;

                while ( ($course_completed_arr=fgetcsv($course_completed) )  !== false){
                    $course_completed_arr = array_map('trim', $course_completed_arr); //trims all cols in row
                    $row_num++;
                    $row_errors = [];
                    $skip_line = FALSE;
                    for ($i=0; $i<sizeof($course_completed_arr); $i++){
                        if ($course_completed_arr[$i]===''){
                            $skip_line = TRUE;
                            $row_errors[] = "blank {$field[$i]}";
                        }
                    }
                    if ($skip_line == False){
                        $course_completedObj= new CourseCompleted($course_completed_arr[0],$course_completed_arr[1]);
                        $row_errors = $course_completedDAO->add($course_completedObj);
                    }
                    if (!empty($row_errors)){
                        $errors[] = [$filename, $row_num, $row_errors];
                    }
                    else{
                        $course_completed_success++;
                    }
                }
                fclose($course_completed);

                //redoes validation for prerequisite completion check
                $course_completed = @fopen($course_completed_path, "r");
                $fields=fgetcsv($course_completed);//gets rid of headers for course_completed
                $filename = "course_completed.csv";
                $row_num = 1;

                while ( ($course_completed_arr=fgetcsv($course_completed) )  !== false){
                    $course_completed_arr = array_map('trim', $course_completed_arr); //trims all cols in row
                    $row_num++;
                    $row_errors = [];
                    $skip_line = FALSE;
                    for ($i=0; $i<sizeof($course_completed_arr); $i++){
                        if ($course_completed_arr[$i]===''){
                            $skip_line = TRUE;
                        }
                    }
                    if ($skip_line == False){
                        $course_completedObj= new CourseCompleted($course_completed_arr[0],$course_completed_arr[1]);
                        $userid = $course_completedObj->userid;
                        $code = $course_completedObj->code;
                        //checks if course_completed row exists && checks if all prerequisites completed)
                        if (!empty($course_completedDAO->completed_course($userid, $code)) && !$course_completedDAO->completed_prerequisite($userid, $code)){
                            $course_completedDAO->delete($userid,$code);
                            $errors[] = [$filename, $row_num, ["invalid course completed"]];
                            $course_completed_success--;
                        }
                    }
                }
                fclose($course_completed);
                unlink($course_completed_path);
                

                //processes bid.csv
                $bid_arr=fgetcsv($bid);
                while ( ($bid_arr=fgetcsv($bid) )  !== false){
                    $bidObj= new Bid($bid_arr[0],$bid_arr[1],$bid_arr[2],$bid_arr[3]);
                    $bidDAO->add($bidObj);
                    $bid_success++;
                }
                fclose($bid);
				unlink($bid_path);
            }


            }
        }

        $roundDAO = new RoundStatusDAO();
        $roundDAO->startRound(1);

        $lines_loaded = [
                        "student.csv" => $student_success,
                        "course.csv" => $course_success,
                        "section.csv" => $section_success,
                        "prerequisite.csv" => $prerequisite_success,
                        "course_completed.csv" => $course_completed_success,
                        "bid.csv" => $bid_success
                        ];
        
        return [$lines_loaded, $errors];  
    }





?>