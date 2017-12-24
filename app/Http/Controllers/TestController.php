<?php

namespace App\Http\Controllers;

use App\Item;
use App\Paintseed;
use App\Pattern;
use App\Steam;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
	public function set_steams_task($id)
	{

		dd($id);

		$tasks = Task::with('item')->where('site_id', '=', $id)
		             ->where('pattern','!=', '')->get();

		foreach ($tasks as $task){
			$arr = array_unique($task->item->patterns->where('name', '=', $task->pattern)->pluck('value')->toArray());
			$arr = $task->item->paintseeds->whereIn('value', $arr)->pluck('item_id')->toArray();
			foreach ($arr as $item){
				Steam::create(['steam_id' => $item, 'task_id' => $task->id]);
			}
		}

		return 'ok';
	}

	public function set_patterns_name()
	{

//		dd(Paintseed::where('pattern_name', '!=', null)->distinct()->count());
		set_time_limit(0);
		$patterns = Pattern::all();
		foreach ($patterns as $pattern){
			$paintseeds = Paintseed::where('item_id', '=', $pattern->item_id)->where('value', '=', $pattern->value)->get();
			if (count($paintseeds)) {
				foreach ($paintseeds as $paintseed){
					$paintseed->pattern_name =  $pattern->name;
					$paintseed->save();
				}
//				$pattern->delete();
			}
		}
	}

	public function get_patterns()
    {
        set_time_limit(0);
        $json = json_encode(Pattern::with('item')->get());
        $json = str_replace('\u2605', '★', $json);
        $json = str_replace('\u2122','™', $json);
        return $json;
    }

    public function delete_patterns()
    {
		dd(2);
        DB::table('paintseeds')->delete();
    }

    public function set_patterns()
    {

//        dd(1);
        set_time_limit(0);
        $patterns = json_decode('[{"steam":12950656952,"float":0.001362945418804884,"value":9,"item_name":"★ StatTrak™ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":10740842622,"float":0.0040171523578464985,"value":589,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":7239761736,"float":0.006424915045499802,"value":323,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":5907686952,"float":0.006991757545620203,"value":208,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":9516913040,"float":0.0070985350757837296,"value":453,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12804531373,"float":0.007172996178269386,"value":383,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":10144689212,"float":0.007360265590250492,"value":954,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12893021641,"float":0.007398574147373438,"value":482,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":7592693671,"float":0.007738898973912001,"value":745,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":3610017006,"float":0.00785641185939312,"value":890,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12985452494,"float":0.007865739054977894,"value":753,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":10550819011,"float":0.007977479137480259,"value":267,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":11353172790,"float":0.00860708486288786,"value":683,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12021274102,"float":0.008672901429235935,"value":668,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":8704866530,"float":0.008722360245883465,"value":233,"item_name":"★ StatTrak™ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":9617101395,"float":0.008894708938896656,"value":209,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":6525273550,"float":0.008965406566858292,"value":738,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":9567218832,"float":0.009163076989352703,"value":50,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":2671755892,"float":0.009165054187178612,"value":372,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":9486634056,"float":0.009321891702711582,"value":921,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12258740708,"float":0.009354674257338047,"value":112,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":3359618163,"float":0.009419727139174938,"value":859,"item_name":"★ StatTrak™ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":9727363832,"float":0.009568359702825546,"value":181,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":11127509840,"float":0.009612574242055416,"value":295,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13097953933,"float":0.010061634704470634,"value":577,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12685771565,"float":0.010755130089819431,"value":594,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":7592691193,"float":0.01098825316876173,"value":304,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12470519366,"float":0.011266346089541912,"value":573,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":11999152825,"float":0.01137479767203331,"value":94,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":10332731830,"float":0.011399466544389725,"value":906,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":5599937884,"float":0.011647608131170273,"value":824,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":10918595009,"float":0.011692168191075325,"value":895,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":10710630886,"float":0.011707531288266182,"value":396,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12720108509,"float":0.011769101023674011,"value":516,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12618013376,"float":0.012875969521701336,"value":709,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":7636125880,"float":0.012992992997169495,"value":217,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12268560048,"float":0.013644457794725895,"value":574,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12024996052,"float":0.014319132082164288,"value":962,"item_name":"★ StatTrak™ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12999634116,"float":0.015098917298018932,"value":543,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":11686317481,"float":0.016803808510303497,"value":747,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":11552573520,"float":0.01710440404713154,"value":923,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12015989339,"float":0.017184704542160034,"value":947,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":9562611709,"float":0.01722591184079647,"value":534,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12749683645,"float":0.017712729051709175,"value":590,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":11708921031,"float":0.017824674025177956,"value":614,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12237159836,"float":0.01787368766963482,"value":992,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":11796297160,"float":0.018009686842560768,"value":971,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":11398393350,"float":0.018081173300743103,"value":984,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13104079976,"float":0.01895735412836075,"value":467,"item_name":"★ StatTrak™ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12652277659,"float":0.01922565884888172,"value":124,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":8550915398,"float":0.019405802711844444,"value":560,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12314497624,"float":0.019704176113009453,"value":283,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":2703620367,"float":0.019840050488710403,"value":914,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":7540591872,"float":0.019925368949770927,"value":303,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":10455682488,"float":0.02000674046576023,"value":744,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13055555060,"float":0.020513663068413734,"value":603,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":6159035109,"float":0.022557711228728294,"value":872,"item_name":"★ StatTrak™ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":11671239791,"float":0.022709889337420464,"value":454,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13051694646,"float":0.02282070182263851,"value":934,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13023200319,"float":0.023599280044436455,"value":325,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":11873300079,"float":0.023616747930645943,"value":452,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12891917677,"float":0.024602770805358887,"value":829,"item_name":"★ StatTrak™ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":7945374330,"float":0.024650510400533676,"value":93,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":8281759902,"float":0.02564089745283127,"value":649,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12827025357,"float":0.026192549616098404,"value":462,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13113794240,"float":0.026252545416355133,"value":101,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":9656866109,"float":0.026832886040210724,"value":251,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":3678477951,"float":0.02702268585562706,"value":473,"item_name":"★ StatTrak™ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":9072539911,"float":0.027277011424303055,"value":286,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":6738631197,"float":0.0272962786257267,"value":664,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":11867503020,"float":0.027395043522119522,"value":582,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":3742095984,"float":0.028581291437149048,"value":795,"item_name":"★ StatTrak™ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13099889151,"float":0.02961387299001217,"value":730,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":7035266759,"float":0.029944688081741333,"value":370,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":3734362134,"float":0.029956622049212456,"value":230,"item_name":"★ StatTrak™ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":9038198916,"float":0.029976412653923035,"value":350,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12786990501,"float":0.03128993138670921,"value":891,"item_name":"★ StatTrak™ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13069888016,"float":0.03173767402768135,"value":504,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12482567647,"float":0.031901244074106216,"value":30,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":3824274371,"float":0.031932439655065536,"value":136,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12927310249,"float":0.03194930776953697,"value":526,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":11987347665,"float":0.03206601366400719,"value":726,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13105063976,"float":0.03236362710595131,"value":362,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":7817563978,"float":0.03237250819802284,"value":429,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12893906093,"float":0.03265305608510971,"value":827,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12421260435,"float":0.03270110860466957,"value":4,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":7629957311,"float":0.03299659863114357,"value":936,"item_name":"★ StatTrak™ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12297731088,"float":0.03306078538298607,"value":903,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":7987764866,"float":0.03354158625006676,"value":437,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12659471560,"float":0.03364802896976471,"value":848,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12296970630,"float":0.03374724090099335,"value":140,"item_name":"★ StatTrak™ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":11256345774,"float":0.03379553183913231,"value":375,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":10889934339,"float":0.03387810289859772,"value":101,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12922876544,"float":0.03391960635781288,"value":185,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":10310750560,"float":0.03422681614756584,"value":196,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13021523264,"float":0.03426365181803703,"value":291,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13022058926,"float":0.03429796174168587,"value":788,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":10568316694,"float":0.03444850072264671,"value":121,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13085576382,"float":0.03458966314792633,"value":476,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":10488321722,"float":0.03468138352036476,"value":923,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":10488321722,"float":0.03468138352036476,"value":923,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12763155532,"float":0.03481011837720871,"value":415,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":11997673295,"float":0.03492248058319092,"value":154,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12370753353,"float":0.03495199605822563,"value":148,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":11181882403,"float":0.03511958569288254,"value":291,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12806845923,"float":0.035220932215452194,"value":109,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13087175621,"float":0.035233817994594574,"value":857,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":10980426277,"float":0.03528497740626335,"value":328,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12868319132,"float":0.035353343933820724,"value":510,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":4035093293,"float":0.03537968546152115,"value":591,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":10065653558,"float":0.0355575829744339,"value":759,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":9823573008,"float":0.03566323593258858,"value":238,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12694485716,"float":0.035805996507406235,"value":628,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":11899791440,"float":0.035930659621953964,"value":92,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":6655316439,"float":0.03595797345042229,"value":679,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":9443899150,"float":0.0373140312731266,"value":415,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13031508518,"float":0.03897882252931595,"value":609,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12998213005,"float":0.04191022366285324,"value":475,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13005760966,"float":0.04368147626519203,"value":467,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13101367149,"float":0.044106319546699524,"value":371,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13102978519,"float":0.04433094710111618,"value":120,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13040129284,"float":0.044640589505434036,"value":123,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":11434980747,"float":0.044850949198007584,"value":664,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12711847238,"float":0.04495169222354889,"value":222,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12653215998,"float":0.04542221501469612,"value":448,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12894097631,"float":0.05436806008219719,"value":944,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":7608687941,"float":0.05444927141070366,"value":553,"item_name":"★ StatTrak™ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12262067180,"float":0.05600894242525101,"value":953,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12934243021,"float":0.056025903671979904,"value":421,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13092675676,"float":0.05894319340586662,"value":13,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13098493706,"float":0.06096808612346649,"value":673,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":7395149901,"float":0.06098445877432823,"value":329,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13046647305,"float":0.06173798441886902,"value":148,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":8030640760,"float":0.06225183978676796,"value":545,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":11862227130,"float":0.0642249584197998,"value":19,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":13023330510,"float":0.06825989484786987,"value":477,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":7316643989,"float":0.0688682347536087,"value":168,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":1277255064,"float":0.06986723095178604,"value":856,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Factory New)"},{"steam":12913513786,"float":0.07197170704603195,"value":79,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Minimal Wear)"},{"steam":10686875539,"float":0.076206274330616,"value":421,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Minimal Wear)"},{"steam":10115632690,"float":0.07828974723815918,"value":723,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Minimal Wear)"},{"steam":12831948174,"float":0.07945398986339569,"value":944,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Minimal Wear)"},{"steam":12831948174,"float":0.07945398986339569,"value":944,"item_name":"★ M9 Bayonet | Doppler Black Pearl (Minimal Wear)"}]');
        foreach ($patterns as $pattern){
            $item_id = Item::where('full_name', '=', $pattern->item_name)->first();
            if ($item_id) {
                $item_id = $item_id->id;
                $val = '1001';
                try {
                    $val = $pattern->value;
                } catch (\Exception $exception) {
                }
                Paintseed::create(['item_id' => $item_id, 'value' => $val,
                    'steam' => $pattern->steam, 'float' => $pattern->float, 'pattern_name' => null]);
            }
        }

        return 'OK';
    }

    public function get_tasks($site_id, $username)
    {
        set_time_limit(0);
        $tasks = Task::with('item.paintseeds')->where('site_id', '=', $site_id)
            ->where('client','=', $username)->get();

        $result = [];
        foreach ($tasks as $task) {
            $paintseeds = $task->item->paintseeds;
            if ($task->float) $paintseeds = $paintseeds->where('float', '<=', $task->float);
            if ($task->pattern) $paintseeds = $paintseeds->where('pattern_name', '=', $task->pattern);
            $paintseeds = $paintseeds->pluck('steam')->toArray();
            $result[$task->id] = $paintseeds;
        }

        dd($result);

//        return json_encode($result);
    }

}