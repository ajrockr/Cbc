<?php
// src/Service/DataTableService.php
namespace App\Service;
 
class DataTableService {
 
    public function getData($request, $repository, $furtherConditions=""): string {
         
        // Get the parameters from the Ajax Call
        if ($request->getMethod() == 'POST') {
            $parameters = $request->request->all();
            $draw = $parameters['draw'];
            $start = $parameters['start'];
            $length = $parameters['length'];
            $search = $parameters['search'];
            $orders = $parameters['order'];
            $columns = $parameters['columns'];
        }
        else
            die;
         
        //Order the Entries for the table
        foreach ($orders as $key => $order){
            $orders[$key]['name'] = $columns[$order['column']]['name'];
        }
 
        // Get results from the Repository
        $results = $repository->getTableData($start, $length, $orders, $search, $columns, $furtherConditions = null);
        $objects = $results["results"];
         
        // Get total number of objects
        $total_objects_count =  $repository->countObjects();
         
        // Get total number of results
        $selected_objects_count = count($objects);
         
        // Get total number of filtered data
        $filtered_objects_count = $results["countResult"];
         
        // Construct response
        $response = '{
            "draw": '.$draw.',
            "recordsTotal": '.$total_objects_count.',
            "recordsFiltered": '.$filtered_objects_count.',
            "data": [';
         
        $i = 0;
         
        foreach ($objects as $key => $object) {
            $response .= '["';
             
            $j = 0;
            $nbColumn = count($columns);
            foreach ($columns as $key => $column) {
                // In all cases where something does not exist or went wrong, return -
                $responseTemp = "-";
                 
                $functionName = 'get'.$column['name'];
 
                if ($functionName != "get")
                    $responseTemp = $object->$functionName();
                
               // Add the found data to the json
               $response .= $responseTemp;
                
               if(++$j !== $nbColumn)
               $response .='","';
            }
             
            $response .= '"]';
             
            // Not on the last item
            if(++$i !== $selected_objects_count)
                $response .= ',';
        }
         
        $response .= ']}';
 
        return $response;
    }
         
    public function countObjectsInTable($countQuery, $table) {
        return $countQuery->select("COUNT($table)");
    }
 
    public function setLength($countQuery, $length) {
        $countResult = $countQuery->getQuery()->getSingleScalarResult();
        if ($length == -1) {
            $length = $countResult;
        }
        return $countResult;
    }
 
    public function addJoins($query, $countQuery, $joins) {
        for($i = 0; $i < count($joins); $i++) {
            $query->join($joins[$i].$joins[$i][1], $joins[$i][2]);
            $countQuery->join($joins[$i].$joins[$i][1], $joins[$i][2]);             
        } 
    }
 
    public function addConditions($query, $conditions) {
        if ($conditions != null) {
            // Add condition
            $query->where($conditions);
            $countQuery->where($conditions);
        }
    }
 
    public function performSearch($query, $countQuery,$table, $columns, $search) {
     
        $searchItem = $search['value'];
        $searchQuery = "";
         
        for($i = 0; $i < count($columns); $i++) {
 
            if($i < count($columns)-1) {
                if($columns[$i]['searchable'] == "true" && $columns[$i]['name'] != "")
                    $searchQuery .= $table.'.'.$columns[$i]['name'].' LIKE '.'\'%'.$searchItem.'%\''.' OR ';
            }
            else {
                if($columns[$i]['searchable'] == "true" && $columns[$i]['name'] != "")
                    $searchQuery .= $table.'.'.$columns[$i]['name'].' LIKE '.'\'%'.$searchItem.'%\'';
            }
 
        }
        $query->andWhere($searchQuery);
        $countQuery->andWhere($searchQuery);
    }   
     
    public function addLimits($query, $start, $length) {
        return $query->setFirstResult($start)->setMaxResults($length);
    }
     
    public function performOrdering($query, $orders, $table) {
        foreach ($orders as $key => $order) {
            if ($order['name'] != '') {
                 
                $orderColumn = null;
                 
                $orderColumn = "{$table}.{$order['name']}";
                     
                if ($orderColumn !== null) {
                    $query->orderBy($orderColumn, $order['dir']);
                }
            }
        }
    }
}