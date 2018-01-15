<?php

/**
 * /lists/{name} get details about a specific id
 * ?useCache=false to force reload details.
 */
$app->get('/', function ($request, $response, $args) {
    require __DIR__ . '/../services/lists.php';
    $service = new ListService();
    $data = $service->getPopularUndergroundMovies();
    $newResp = $response->withJson($data);
    return $newResp;

});

$getListInfo = function ($listId, $useCache = true) {
    $service = new ListService();
    $listId = str_replace($listId, '/', '-');
    $listId = str_replace($listId, '\\', '-');
    switch ($listId) {
        case 'movies-popular-underground':
            return $service->getPopularUndergroundMovies($useCache);
        case 'movies-recent-underground':
            return $service->getRecentUndergroundMovies($useCache);
        case 'series-new':
            return $service->getNewSeries($useCache);
        case 'series-popular':
            return $service->getPopularSeries($useCache);
        default:
            return [];
    }
};


$app->get('/lists/{lists}', function ($request, $response, $args) use ($getListInfo) {

    $lists = str_split(",", $args['lists']);
    if (sizeof($lists)) {
        $data = [];
        foreach ($lists as $list) {
            $data[$list] = $getListInfo[$list];
        }
        $newResp = $response->withJson($data);
    } else {
        $newResp = $response->withJson(['error' => "no lists provided, you can provide list like /lists?lists=[movies-popular,series-popular]"], 404);
    }

    return $newResp;
});

$app->get('/lists/{group}/{name}', function ($request, $response, $args) use ($getListInfo) {
    require __DIR__ . '/../services/lists.php';

    $data = null;
    $useCache = $request->getQueryParam('useCache') !== "false";
    $newResp = $response->withJson(['error' => "Could not get list details"], 404);
    $data = $getListInfo($args['group'] . '-' . $args['name'], $useCache);


    if ($data != null) {
        $newResp = $response->withJson($data);
    }

    return $newResp;
});
