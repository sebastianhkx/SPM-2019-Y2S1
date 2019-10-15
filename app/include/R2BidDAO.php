<?php
class R2BidDAO{

    public function getr2bidinfo($bidobj){
        $sql = 'SELECT * from r2_bid_info where course=:course and section=:section';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $bidobj->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $bidobj->section, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = [];
        $status = FALSE;

        if ($row = $stmt->fetch()){
            $status = TRUE;
            $result = array("course"=>$row["course"], "section"=>$row['section'], "min_amount"=>$row["min_amount"],"vacancy"=>$row['vacancy']);
        }
        return $result;
    }

    public function addbidinfo($result_info){
        // this function take in an bid object, the clearing price 
        $sql = "INSERT IGNORE INTO r2_bid_info(course, section,min_amount,vacancy) VALUES (:course, :section, :min_amount,:vacancy)";

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $result_info[0], PDO::PARAM_STR);
        $stmt->bindParam(':section', $result_info[1], PDO::PARAM_STR);
        $stmt->bindParam(':min_amount', $result_info[2], PDO::PARAM_INT);
        $stmt->bindParam(':vacancy', $result_info[3], PDO::PARAM_INT);



        $isAddOk = FALSE;
        if ($stmt->execute()){
            $isAddOk = TRUE;
        }
        
        return $isAddOk;
    }

    public function getBid($prv_clearingprice,$bidarray){
        $sql = 'SELECT count(userid) as num from bid where course=:course and section=:section and amount >= :amount';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $bidarray['course'], PDO::PARAM_STR);
        $stmt->bindParam(':section', $bidarray['section'], PDO::PARAM_STR);
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
        $bid_dao = new BidDAO();
        $result = $this->getr2bidinfo($bidobj);
        $output = [];
        $bids = $bid_dao->retrieveByCourseSection([$bidobj->course,$bidobj->section]);
        $totalbids = count($bids);
        $count = 0;
        if($totalbids <= $result['vacancy']){
            foreach($bids as $bid){
                $output[] = [$bid->amount,"Successful"];
            }
        }
        if($totalbids == $result['vacancy']){
            $lowestprice = $this->getminimunprice($bidobj);
            $result['min_amount'] = $lowestprice + 1;
        }
        if($totalbids > $result['vacancy']){
            foreach($bids as $bid){
                if($bid->amount >= $result['min_amount']){
                    $state = "Successful";
                    $count += 1;
                    $lowestprice = $bid->amount;
                }
                elseif($bid->amount == $result['min_amount']-1){
                    $state = "Unsuccessful";
                }
                else{
                    $state = 'Unsuccessful. Bid too low!';
                }
                $output[] = [$bid->amount,$state]; 
            }
            if($count == $result['vacancy']){
                $result['min_amount'] = $lowestprice + 1;
            }
            if($count < $result['vacancy']){
                $lowestprice = $output[$result['vacancy']-1][0];
                $num_morethan_lowest_price = $this-> getBid($lowestprice,$result);
                if($num_morethan_lowest_price == $result['vacancy']){
                    for($i=$count;$i<$result['vacancy'];$i++){
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

        $stmt->bindParam(':course', $result['course'], PDO::PARAM_STR);
        $stmt->bindParam(':section', $result['section'], PDO::PARAM_STR);
        $stmt->bindParam(':min_amount', $result['min_amount'], PDO::PARAM_INT);

        $stmt->execute();

        return $output;
    }

    public function deleteInfo(){
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
    
}
