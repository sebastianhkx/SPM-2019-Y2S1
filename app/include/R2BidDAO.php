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
            $result = array("course"=>$row["course"], "section"=>$row['section'], "amount"=>$row["amount"],"size"=>$row['size']);
        }
        return $result;
    }
    
}
