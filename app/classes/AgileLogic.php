<?php

/**
 * Created by PhpStorm.
 * User: sven
 * Date: 2017/7/7 0007
 * Time: 下午 3:56
 */

namespace main\app\classes;

use main\app\model\issue\IssueStatusModel;
use main\app\model\issue\IssueModel;
use main\app\model\agile\SprintModel;
class AgileLogic
{
    const BACKLOG_VALUE = 0;

    public function getBacklogs($projectId)
    {
        $params = [];
        $sql = " WHERE sprint=" . self::BACKLOG_VALUE;

        // 所属项目
        $sql .= " AND project_id=:project_id";
        $params['project_id'] = $projectId;

        // 非关闭状态
        $issueStatusModel = new IssueStatusModel();
        $closedStatusId = $issueStatusModel->getIdByKey('closed');
        $sql .= " AND status!=:status_id";
        $params['status_id'] = $closedStatusId;

        $order = " Order By priority Asc,id DESC";

        $model = new IssueModel();
        $table = $model->getTable();
        try {
            $field = 'id,pkey,issue_num,project_id,reporter,assignee,issue_type,summary,priority,
            resolve,status,created,updated';
            // 获取总数
            $sqlCount = "SELECT count(*) as cc FROM  {$table} " . $sql;
            $count = $model->db->getOne($sqlCount, $params);

            $sql = "SELECT {$field} FROM  {$table} " . $sql;
            $sql .= ' ' . $order;
            //print_r($params);
            //echo $sql;die;
            $arr = $model->db->getRows($sql, $params);
            return [true, $arr, $count];
        } catch (\PDOException $e) {
            return [false, $e->getMessage(), 0];
        }
    }

    public function getSprints($projectId)
    {
        $params = [];
        $params['project_id'] = intval($projectId);
        $model = new SprintModel();
        $rows = $model->getRows('*',$params,null,'id','DESC');
        return $rows;
    }
    public function getSprintIssues($projectId, $sprintId)
    {
        $params = [];
        $sql = " WHERE sprint=" . intval($sprintId);

        // 所属项目
        $sql .= " AND project_id=:project_id";
        $params['project_id'] = $projectId;

        $order = " Order By priority Asc,id DESC";

        $model = new IssueModel();
        $table = $model->getTable();
        try {
            $field = 'id,pkey,issue_num,project_id,reporter,assignee,issue_type,summary,priority,
            resolve,status,created,updated';
            // 获取总数
            $sqlCount = "SELECT count(*) as cc FROM  {$table} " . $sql;
            $count = $model->db->getOne($sqlCount, $params);

            $sql = "SELECT {$field} FROM  {$table} " . $sql;
            $sql .= ' ' . $order;
            //print_r($params);
            //echo $sql;die;
            $arr = $model->db->getRows($sql, $params);
            return [true, $arr, $count];
        } catch (\PDOException $e) {
            return [false, $e->getMessage(), 0];
        }
    }
}
