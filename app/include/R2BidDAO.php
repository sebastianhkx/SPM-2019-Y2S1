<?php
class R2BidDAO{

    public function getr2bidinfo($bidobj){
        //this function take in a bid object and return an object with section's minimum and vacancy
        $sql = 'SELECT * from r2_bid_info where course=:course and section=:section';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $bidobj->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $bidobj->section, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = [];

        if ($row = $stmt->fetch()){
            $result = new R2Bid($row['course'],$row['section'],$row['min_amount'],$row['vacancy']);
            //$result = array("course"=>$row["course"], "section"=>$row['section'], "min_amount"=>$row["min_amount"],"vacancy"=>$row['vacancy']);
        }
        return $result;
    }

    public function addbidinfo($r2Bid){
        // this function take in an object for R2 bid info 
        $sql = "INSERT IGNORE INTO r2_bid_info(course, section,min_amount,vacancy) VALUES (:course, :section, :min_amount,:vacancy)";

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $r2Bid->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $r2Bid->section, PDO::PARAM_STR);
        $stmt->bindParam(':min_amount', $r2Bid->min_amount, PDO::PARAM_INT);
        $stmt->bindParam(':vacancy', $r2Bid->vacancy, PDO::PARAM_INT);



        $isAddOk = FALSE;
        if ($stmt->execute()){
            $isAddOk = TRUE;
        }
        
        return $isAddOk;
    }

    public function getBid($prv_clearingprice,$r2bid){
        // this function take in a bid object and a clearing price to get the total number of bids that more than or equal to the clearing price
        // return a number
        $sql = 'SELECT count(userid) as num from bid where course=:course and section=:section and amount >= :amount';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $r2bid->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $r2bid->section, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $prv_clearingprice, PDO::PARAM_STR);

        $result = 0;
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        if($row = $stmt->fetch()){
            $result = $row['num'];
        }

        return $result;
    }

    public function getminimunprice($bidobj){
        //this function take in a bid object and return the minimun amount for that section 
        $sql = 'SELECT MIN(amount) as minimum from bid where course=:course and section=:section order by amount DESC';
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $bidobj->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $bidobj->section, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = 0;

        if ($row = $stmt->fetch()){
            $result = $row['minimum'];
        }
        return $result;
    }

    public function updateBidinfo($bidobj){
        //this function take in a bid object to get total bids for the section with status
        //output = [[bid_amount1,'status1'],[bid_amount2,status2]]
        $courseEnrolled_dao =  new CourseEnrolledDAO;
        $bid_dao = new BidDAO();
        $r2Bid_info = $this->getr2bidinfo($bidobj);
        $output = [];
        //bids have been sorted based on amount in desc
        $bids = $bid_dao->retrieveByCourseSection([$bidobj->course,$bidobj->section]);
        $totalbids = count($bids);
        $count = 0; 
        if($totalbids <= $r2Bid_info->vacancy){
            foreach($bids as $bid){
                $output[] = [$bid->amount,"Successful"];
            }
        }
        if($totalbids == $r2Bid_info->vacancy){
            $lowestprice = $this->getminimunprice($bidobj);
            $r2Bid_info->min_amount = $lowestprice + 1;
        }
        if($totalbids > $r2Bid_info->vacancy){
            foreach($bids as $bid){
                if($bid->amount >= $r2Bid_info->min_amount ){
                    $state = "Successful";
                    $count += 1;
                    $lowestprice = $bid->amount;
                }
                elseif($bid->amount == $r2Bid_info->min_amount - 1){
                    $state = "Unsuccessful";
                }
                else{
                    $state = 'Unsuccessful. Bid too low!';
                }
                $output[] = [$bid->amount,$state]; 
            }
            if($count == $r2Bid_info->vacancy){
                $r2Bid_info->min_amount = $lowestprice + 1;
            }
            if($count < $r2Bid_info->vacancy){
                $lowestprice = $output[$r2Bid_info->vacancy - 1][0];
                $num_morethan_lowest_price = $this-> getBid($lowestprice,$r2Bid_info);
                if($num_morethan_lowest_price == $r2Bid_info->vacancy){
                    for($i=$count;$i < $r2Bid_info->vacancy;$i++){
                        $output[$i][1] = "Successful";
                    }
                }
            }
        }

        //var_dump($result);
        $sql = 'UPDATE r2_bid_info SET min_amount = :min_amount  WHERE course=:course AND section = :section';
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $r2Bid_info->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $r2Bid_info->section, PDO::PARAM_STR);
        $stmt->bindParam(':min_amount', $r2Bid_info->min_amount, PDO::PARAM_INT);

        $stmt->execute();

        return $output;
    }

    public function deleteInfo(){
        //this function is used to empty the r2_bid_info table
        $sql = 'TRUNCATE TABLE r2_bid_info';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $count = $stmt->rowCount();

        $stmt = null;
        $conn = null; 
    }

    public function r2dropSection($sectionobj){
        //this function take in a section object to reset the vacancy for that section
        $sql = 'UPDATE r2_bid_info SET vacancy = vacancy + 1 WHERE course=:course AND section = :section';
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $sectionobj->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $sectionobj->section, PDO::PARAM_STR);

        $isOK = FALSE;
        if($stmt->execute()){
            $isOK = TRUE;
        }
        $stmt = null;
        $conn = null; 

        return $isOK ;
    }
    
    function searchCourseSection($bid){
        //this function takes in a bid object to check if course and section exists
        //return an index array incluing errors and an associative array of 
        $section_dao = new SectionDAO();
        $courseEnrolled_dao = new CourseEnrolledDAO(); 
        $section = $section_dao->retrieveBySection($bid);
        $result = [];
        $errors = null;
        if($section == null){
            $errors[] = 'invalid course/section';
        }
        
        if($errors == null){
            $courseEnrolledObjs = $courseEnrolled_dao->retrieveByCourseSection([$bid->course, $bid->section]);
            //var_dump($courseEnrolledObjs);
            if ($courseEnrolledObjs != null){
                $enrolled = sizeof($courseEnrolledObjs);
            }
            else{
                $enrolled = 0;
            }
            $size = $section_dao->retrieveBySection($bid)->size;
            if ($size-$enrolled<=0){
                $errors[] = 'no vacancy';
            }
        }
  
        if($errors == null){
            $result = $this->updateBidinfo($bid);
        }
        
        return [$result,$errors];
    }

    function checkBidsStatus($bidsobj){
        //this function take in an array of bid object and return an array of bid object and bid status
        $result = [];

        foreach($bidsobj as $bid){
            $state = "Successful";
            $bid_info = $this->updateBidinfo($bid);
            if(!in_array(array($bid->amount,'Successful'),$bid_info)){
                $state = "Unsuccessful";
            }
            $result[] = array('bid'=>$bid,'status'=>$state);
        }
        return $result;
    }

    function checkCourseEnrolled($bid){
        //this function take in a bid object to check number of course enrolled
        $courseEnrolled_dao =  new CourseEnrolledDAO;
        $bid_dao = new BidDAO();
        $coursesEnrolled = $courseEnrolled_dao -> retrieveByUserid($bid->userid);
        $bids = $bid_dao->retrieveByUser($bid->userid);
        $errors = [];
        $availablebid = 5 - sizeof($coursesEnrolled);
        if(sizeof($coursesEnrolled) == 5){
            $errors[] = 'More than 5 courses enrolled';
        }
        if(empty($errors) && $availablebid == sizeof($bids)){
            $errors[] = 'Section limit reached';
        }
        if (empty($errors)){
            $errors = $bid_dao->add($bid);
        }
        return $errors;
    }
}
