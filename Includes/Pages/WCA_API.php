<?php
function getApiData($url){
    $data=GetValue($url,true);
    if(!$data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        SaveValue($url, $data);
        $status=curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    }
    return $data;
}


?>

<h1>Examples of use WCA API</h1>


<?php 
$Params=[
        'user_id' => '118',
        'wca_id' => '2012TERE01',
        'wca_id2' => '2015SOLO01',
        'user_search' => 'SOLOV',
        'competition_id' => 'RamenskoeOpen2019',
        'competition_search' => 'RamenskoeOpen',
        'competition_start_date'=>'2019-09-14',
        'competition_end_date'=>'2019-09-15', 
        'delegate_page'=>5,
        'post_search'=>'RamenskoeOpen',
        'regulation_search'=>'Clock',
    ]
?>
<?php
    $DATAS=[
        'users'=>[
                    ['url'=>'{user_id}','description'=>'user : wca_id may be null'],
                    ['url'=>'{wca_id}','description'=>'user'],
                    ['url'=>'{user_id}?upcoming_competitions=true','description'=>' user & [upcoming_competitions] : wca_id may be null'],
                    ['url'=>'{wca_id}?upcoming_competitions=true','description'=>' user & [upcoming_competitions]'],
                ],
        'delegates'=>[
                    ['url'=>'?page={delegate_page}','description'=>'[users]'],
                ],
        'records'=>[
                    ['url'=>'','description'=>'[records]'],
                ],
        'persons'=>[
                    ['url'=>'{wca_id}','description'=>'person & personal_records'],
                    ['url'=>'{wca_id}/results','description'=>'[results]'],
                    ['url'=>'?q={user_search}','description'=>'[persons & personal_records]'],
                    ['url'=>'?wca_ids={wca_id},{wca_id2}','description'=>'[persons & personal_records]'],
                ],
        'competitions'=>[
                    ['url'=>'{competition_id}','description'=>'competition'],
                    ['url'=>'{competition_id}/registrations','description'=>'[registrations(user_id)]'],
                    ['url'=>'{competition_id}/competitors','description'=>'[persons] : after competition'],
                    ['url'=>'{competition_id}/results','description'=>'[results(wca_id)] : after competition'],
                    ['url'=>'?q={competition_search}','description'=>'[competitions]'],
                    ['url'=>'?start={competition_start_date}&end={competition_end_date}&sort=start_date,name','description'=>'[competitions]'],
                    ['url'=>'{competition_id}/wcif','description'=>'wcif : available only to delegates and organizers'],
                    ['url'=>'{competition_id}/wcif/public','description'=>'[persons & personalBests], [events], wcif/schedule'],
                    ['url'=>'{competition_id}/schedule','description'=>'wcif/schedule'],
                ],
        'scramble-program'=>[
                    ['url'=>'','description'=>'legal TNoodle version'],
                ],
        'search'=>[
                    ['url'=>'?q={user_search}','description'=>'[persons] & [competitions]'],
                    ['url'=>'?q={competition_search}','description'=>'[persons] & [competitions]'],
                    ['url'=>'posts/?q={post_search}','description'=>'[posts]'],
                    ['url'=>'competitions/?q={competition_search}','description'=>'[competitions]'],
                    ['url'=>'users/?q={user_search}','description'=>'[users]'],
                    ['url'=>'regulations/?q={regulation_search}','description'=>'[regulations]'],
                ]
    ];
?>
<?php foreach($DATAS as $group=>$datas){  ?>
    <h2><?= $group ?></h2>
    <ul>
    <?php foreach($datas as $code=>$data){ 
        $m_ulr=$data['url'];
        preg_match_all('/{(.+?)}/', $data['url'], $matches, PREG_OFFSET_CAPTURE);
        foreach($matches[0] as $n=>$m){
            $m_ulr=str_replace($m[0],$Params[$matches[1][$n][0]],$m_ulr);
        }
        $url='https://www.worldcubeassociation.org/api/v0/'.$group.'/'.$m_ulr; ?>
        <li>
            <a target="_blank" href="<?= $url ?>">
                <?= "$group/".$data['url'] ?>
            </a>
            - <?= $data['description']?>
        </li>
    <?php } ?>
    </ul>    
<?php } ?>