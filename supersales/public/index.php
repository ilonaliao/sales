<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once '../vendor/autoload.php';
require_once '../config/config.php';

$container = new \Slim\Container(include '../config/container.config.php');
$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c->view->render($response, 'system/404.html.twig') 
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html');
    };
};

$container['errorHandler'] = function ($c) {
    return function ($request, $response, $e) use ($c) {
    	$this->slack->log($e);
        return $c->view->render($response, 'system/500.html.twig') 
            ->withStatus(500)
            ->withHeader('Content-Type', 'text/html');
    };
};

$app = new \Slim\App($container);
$app->add(function (Request $request, Response $response, $next) {
	//aidma
	// if(SRV_ENV_FLAG == 1){
		//is LAB
		// $aidma = ['elementid'=>603,'webid'=>48,'channelid'=>130,'boardid'=>603];
	// }else{
		$aidma = ['elementid'=>527,'webid'=>14,'channelid'=>75,'boardid'=>527];
	// }
	$this->get('view')->getEnvironment()->addGlobal('aidma', $aidma);

	$host = $request->getUri()->getHost();
	$uri = $this->get('request')->getUri();
	$basePath = $request->getUri()->getBasePath();
	$queryString = $request->getQueryParams();
	$this->get('view')->getEnvironment()->addGlobal('host', $host);
	$this->get('view')->getEnvironment()->addGlobal('uri', $uri);
	$this->get('view')->getEnvironment()->addGlobal('basePath', $basePath);
	$this->get('view')->getEnvironment()->addGlobal('queryString', $queryString);

	$json = json_decode(Util::readFilebyJson(JSON_DATA_PATH.'/nav.json'));
	$this->get('view')->getEnvironment()->addGlobal('navReport', $json->report);

	$response = $next($request, $response);
	return $response;
});

$app->map(['GET', 'POST'],'/', function (Request $request, Response $response) {
	$json = json_decode(Util::readFilebyJson(JSON_DATA_PATH.'/index.json'));
    return $this->view->render($response, 'index.twig', [
        'navActive'=>'index',
        'json'=>$json,
        'breadCrumbs'=>'',
    ]);
});

$app->map(['GET', 'POST'],'/jobs/{id:[1|2|3|4]+}', function (Request $request, Response $response, $args) {
	$type = $args['id'];
	$json = json_decode(Util::readFilebyJson(JSON_DATA_PATH.'/jobs.json'));
	$getParams = $request->getQueryParams();

	$asideText = array('電銷人員','國內業務人員','金融理專','不動產經紀人');
	switch ($type) {
		case '1':
			//電銷人員
			$getParams['jobcat'] = '2005003007';
			break;
		case '2':
			//國內業務人員
			$getParams['jobcat'] = '2005003001,2005003004';
			break;
		case '3':
			//金融理專
			$getParams['indcat'] = '1004000000';
			$getParams['jobcat'] = '2005003004,2003002003,2003002004,2003002005,2003002006,2003002015';
			break;
		case '4'://不動產經紀人
			$getParams['jobcat'] = '2005003009'; 
			break;
		default:
			break;
	}

	//reset json data
	$resetFields = ['sponsor','banner'];
	foreach ($resetFields as $fieldKey => $fieldName) {
		$temp = '';
		if(!empty($json->{$fieldName}) && !empty($json->{$fieldName}[$type-1])){
			$temp = $json->{$fieldName}[$type-1];
		}
		unset($json->{$fieldName});
		$json->{$fieldName} = $temp;
	}
	
	$joblist = array();
	try{
		$joblistModule = new JoblistModule($getParams);
		$joblist = json_decode($joblistModule->getNccJobsApi());
	} catch (Exception $e) {
		$this->slack->log($e);
	}
    return $this->view->render($response, 'jobs.twig', [
        'breadCrumbs'=>'高薪職缺',
        'navActive'=>'jobs',
        'joblist'=>$joblist,
        'type'=>$type,
        'json'=>$json,
        'asideText'=>$asideText
    ]);
});

/**
 * ajax 操作儲存工作
 * 1.取儲存工作列表 (getList)
 * 2.新增儲存工作 (add)
 * 3.刪除儲存工作 (delete)
 */
$app->get('/ajax/{action}', function (Request $request, Response $response, $args) {
	if(empty($_COOKIE['ID_CK'])){
		return;
	}
	try {
		$action = $args['action'];
		$idck = $_COOKIE['ID_CK'];
		$get = $request->getQueryParams();
		
		$return = '';
		switch ($action) {
			case 'getList':
			$api = new SaveJobs($idck);
				$return = $api->getList();
				break;
			case 'add':
				if(!empty($get['custno']) && !empty($get['jobno'])){
					$api = new SaveJobs($idck);
					$return = $api->add($get['custno'],$get['jobno']); //TODO 裡面再做驗證！
				}
				break;
			case 'delete':
				if(!empty($get['custno']) && !empty($get['jobno'])){
					$api = new SaveJobs($idck);
					$return = $api->delete($get['custno'],$get['jobno']);
				}
				break;
			default:
				break;
		}
		echo $return;
	} catch (Exception $e) {
		$this->slack->log($e);
		echo json_encode(array('status'=>500));
	}	
});

$app->map(['GET', 'POST'],'/report/{id:[1|2|3|4|5|6|7|8|9|10|11|12]+}', function (Request $request, Response $response, $args) {
	$type = $args['id'];
	$json = json_decode(Util::readFilebyJson(JSON_DATA_PATH.'/report_'.$type.'.json'));
    return $this->view->render($response, 'report.twig', [
        'breadCrumbs'=>'超業關鍵報告',
        'navActive'=>'report',
        'type'=>$type,
        'json'=>$json,
    ]);
});

$app->map(['GET', 'POST'],'/follow[/]', function (Request $request, Response $response) {
	$json = json_decode(Util::readFilebyJson(JSON_DATA_PATH.'/follow.json'));
    return $this->view->render($response, 'follow.twig', [
        'breadCrumbs'=>'跟著前輩這樣做',
        'navActive'=>'follow',
        'json'=>$json,
    ]);
});

$app->map(['GET', 'POST'],'/follow/{id:[1|2|3|4]+}', function (Request $request, Response $response, $args) {
	$id = $args['id'];
	$json = json_decode(Util::readFilebyJson(JSON_DATA_PATH.'/article_'.$id.'.json'));
    return $this->view->render($response, 'article.twig', [
        'breadCrumbs'=>'<a href="/supersales/follow">跟著前輩這樣做</a> > 前輩帶路',
        'navActive'=>'article',
        'id'=>$id,
        'json'=>$json,
    ]);
});

$app->map(['GET', 'POST'],'/show[/]', function (Request $request, Response $response) {
	$json = json_decode(Util::readFilebyJson(JSON_DATA_PATH.'/show.json'));
    return $this->view->render($response, 'show.twig', [
        'breadCrumbs'=>'超業 Talk 秀',
        'navActive'=>'show',
        'json'=>$json,
    ]);
});



$app->run();
