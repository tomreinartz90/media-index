<?php

/**
 * /lists/{name} get details about a specific id
 * ?useCache=false to force reload details.
 */
$app->get('/lists/{group}[/{name}]', function ($request, $response, $args) {
    require __DIR__ . '/../services/lists.php';

    $data = null;
    $service = new ListService();
    $useCache = $request->getQueryParams('useCache', true);

    $newResp = $response->withJson(['error' => "Could not get list details"], 404);
    $data = null;
    if ($args['group'] == 'movies') {
//        var_dump($args);
        switch ($args['name']) {
            case 'popular-underground':
                $data = $service->getPopularUndergroundMovies($useCache);
                break;
            case 'recent-underground':
                $data = $service->getPopularUndergroundMovies($useCache);
                break;
        }
    }

    if ($data != null) {
        $newResp = $response->withJson($data);
    }

    return $newResp;
});
