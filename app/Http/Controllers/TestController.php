<?php

namespace App\Http\Controllers;

use App\Item;
use App\Paintseed;
use App\Pattern;
use App\Steam;
use App\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function set_steams_task($id)
    {

        dd($id);

        $tasks = Task::with('item')->where('site_id', '=', $id)
            ->where('pattern', '!=', '')->get();

        foreach ($tasks as $task) {
            $arr = array_unique($task->item->patterns->where('name', '=', $task->pattern)->pluck('value')->toArray());
            $arr = $task->item->paintseeds->whereIn('value', $arr)->pluck('item_id')->toArray();
            foreach ($arr as $item) {
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
        foreach ($patterns as $pattern) {
            $paintseeds = Paintseed::where('item_id', '=', $pattern->item_id)->where('value', '=', $pattern->value)->get();
            if (count($paintseeds)) {
                foreach ($paintseeds as $paintseed) {
                    $paintseed->pattern_name = $pattern->name;
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
        $json = str_replace('\u2122', '™', $json);
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
        $patterns = json_decode('[{"steam":11680938157,"float":0.0026438389904797077,"value":609,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":11051929517,"float":0.0026783528737723827,"value":612,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":11266750915,"float":0.0029886772390455008,"value":903,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12025033070,"float":0.003899209201335907,"value":624,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12592184056,"float":0.0039041887503117323,"value":269,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13043483017,"float":0.004277953878045082,"value":412,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":11111112443,"float":0.0064142365008592606,"value":929,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":11093233326,"float":0.006652746815234423,"value":556,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12552693246,"float":0.0068276855163276196,"value":269,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12898342519,"float":0.006985106505453587,"value":28,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12963024245,"float":0.0070543428882956505,"value":284,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12904705248,"float":0.007085768971592188,"value":646,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12334077981,"float":0.007092699874192476,"value":760,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":10399745420,"float":0.007095730863511562,"value":888,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12728006449,"float":0.007276172284036875,"value":221,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12986633477,"float":0.0073078470304608345,"value":308,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":10315137907,"float":0.007437699940055609,"value":932,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13113918356,"float":0.007895315997302532,"value":374,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12011166034,"float":0.008021068759262562,"value":648,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13132785430,"float":0.008038859814405441,"value":926,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12238100791,"float":0.008174845017492771,"value":174,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":10943462856,"float":0.00823450367897749,"value":217,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12881677844,"float":0.008347511291503906,"value":243,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12753489173,"float":0.008393918164074421,"value":284,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12838816394,"float":0.008546394295990467,"value":418,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12857864331,"float":0.009105241857469082,"value":973,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12626141381,"float":0.009747914038598537,"value":37,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12753542240,"float":0.009831524454057217,"value":293,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13096851399,"float":0.00993669219315052,"value":231,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12213467677,"float":0.009975243359804153,"value":494,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12546639918,"float":0.010037112981081009,"value":177,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13047615093,"float":0.010066577233374119,"value":739,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12059441904,"float":0.010206330567598343,"value":933,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12911066538,"float":0.010574470274150372,"value":978,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":11173131253,"float":0.010579390451312065,"value":147,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":11046802131,"float":0.010675499215722084,"value":92,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13051173445,"float":0.010895793326199055,"value":747,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13007658758,"float":0.011103736236691475,"value":301,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13026278523,"float":0.01112365908920765,"value":258,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13032377005,"float":0.011239822022616863,"value":68,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13102176392,"float":0.011566746048629284,"value":522,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12402278967,"float":0.011651470325887203,"value":500,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":11550117494,"float":0.011663715355098248,"value":910,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":11229831215,"float":0.011678447015583515,"value":868,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12355907542,"float":0.011830010451376438,"value":624,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":11732999444,"float":0.011856812983751297,"value":508,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12850136925,"float":0.011862318031489849,"value":104,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":11297442938,"float":0.012837696820497513,"value":15,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12449251673,"float":0.01374023873358965,"value":390,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13028474720,"float":0.014370553195476532,"value":448,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12683791098,"float":0.014752036891877651,"value":462,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13104156314,"float":0.014933398924767971,"value":635,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12551048742,"float":0.015939492732286453,"value":716,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13043025854,"float":0.016100959852337837,"value":15,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12317354581,"float":0.01658729463815689,"value":461,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13060824858,"float":0.016687359660863876,"value":133,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12894757075,"float":0.017537705600261688,"value":240,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13067627916,"float":0.017784368246793747,"value":136,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12412137981,"float":0.017964132130146027,"value":230,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12996824023,"float":0.018519729375839233,"value":339,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12649178036,"float":0.018559714779257774,"value":82,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12754463366,"float":0.018562205135822296,"value":226,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12944637432,"float":0.018948419019579887,"value":136,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12769136062,"float":0.019442081451416016,"value":781,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12829402412,"float":0.019599782302975655,"value":487,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12853341073,"float":0.019640836864709854,"value":485,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13130950011,"float":0.01974203623831272,"value":857,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13050321875,"float":0.020121607929468155,"value":182,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13088294265,"float":0.02109638601541519,"value":391,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12938133628,"float":0.021523375064134598,"value":156,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":10561922296,"float":0.02313947305083275,"value":829,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":11170352846,"float":0.023843077942728996,"value":906,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13009739284,"float":0.024530617520213127,"value":538,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":10660121422,"float":0.024590229615569115,"value":394,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13122770370,"float":0.024638095870614052,"value":799,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":9848620320,"float":0.024887168779969215,"value":877,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12732190091,"float":0.02493198774755001,"value":482,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12719464279,"float":0.02530502900481224,"value":318,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13123596610,"float":0.025714153423905373,"value":366,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12984416285,"float":0.026616625487804413,"value":49,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12027252432,"float":0.026905054226517677,"value":574,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12592808937,"float":0.027520891278982162,"value":928,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12830575336,"float":0.028238406404852867,"value":815,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12889451806,"float":0.030235642567276955,"value":348,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12303547738,"float":0.031294599175453186,"value":472,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12863202985,"float":0.03165384754538536,"value":317,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12095576047,"float":0.031730473041534424,"value":163,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12985024120,"float":0.03182104602456093,"value":267,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13043873708,"float":0.031921207904815674,"value":798,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13068146208,"float":0.03192512318491936,"value":241,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13068373900,"float":0.03195476904511452,"value":361,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12814911151,"float":0.03201938048005104,"value":102,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12531679065,"float":0.03206334635615349,"value":532,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12859543773,"float":0.032087575644254684,"value":314,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12736692609,"float":0.03224678337574005,"value":285,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12883054477,"float":0.03235701844096184,"value":960,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12938133687,"float":0.03235802799463272,"value":772,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13107269123,"float":0.032426681369543076,"value":685,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13028254748,"float":0.03248953819274902,"value":341,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":10001006926,"float":0.0327162891626358,"value":723,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":10001006926,"float":0.0327162891626358,"value":723,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12891550396,"float":0.03298838436603546,"value":148,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13102981881,"float":0.033064380288124084,"value":331,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13011574852,"float":0.03313401713967323,"value":953,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13049975771,"float":0.033160846680402756,"value":381,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":10906070575,"float":0.033161621540784836,"value":728,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13116205985,"float":0.03333808854222298,"value":696,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13130616184,"float":0.03341387212276459,"value":539,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12760703539,"float":0.033417828381061554,"value":807,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13057716790,"float":0.03385000675916672,"value":201,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":11585685494,"float":0.03392205014824867,"value":262,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12985402377,"float":0.03408462554216385,"value":244,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12825220004,"float":0.03418275713920593,"value":335,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13030476693,"float":0.03427156060934067,"value":3,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12699157789,"float":0.03438856825232506,"value":979,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12937982424,"float":0.034564562141895294,"value":363,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13096083653,"float":0.03464723005890846,"value":168,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":10751853155,"float":0.03484523296356201,"value":747,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12970563377,"float":0.03503841906785965,"value":27,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13091898120,"float":0.035059113055467606,"value":514,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13036032178,"float":0.035148389637470245,"value":697,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12254508849,"float":0.03526054695248604,"value":480,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12871945410,"float":0.035287149250507355,"value":427,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12808594052,"float":0.03537864238023758,"value":855,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12150365847,"float":0.03541772440075874,"value":599,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12241994105,"float":0.03555924445390701,"value":433,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":11092731730,"float":0.03597383573651314,"value":623,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12938133574,"float":0.03843885660171509,"value":781,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12257676908,"float":0.04203391820192337,"value":773,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13117395216,"float":0.04217887669801712,"value":203,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12883782160,"float":0.04545116424560547,"value":977,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12740970529,"float":0.04569853097200394,"value":520,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12705959024,"float":0.0457688644528389,"value":703,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12936611400,"float":0.04753773286938667,"value":218,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":11647127545,"float":0.04818090423941612,"value":790,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":10984910142,"float":0.049998689442873,"value":210,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12705157058,"float":0.05207059159874916,"value":752,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":11748515853,"float":0.05396495759487152,"value":581,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12926058049,"float":0.05542006343603134,"value":373,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12960756217,"float":0.05575549602508545,"value":702,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12953413841,"float":0.05696428194642067,"value":936,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":13028921351,"float":0.06348250061273575,"value":818,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12938133368,"float":0.06477033346891403,"value":374,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12832881567,"float":0.06683578342199326,"value":864,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12825195342,"float":0.06699459999799728,"value":773,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":10789468632,"float":0.06774511188268661,"value":357,"item_name":"★ Bowie Knife | Doppler Sapphire (Factory New)"},{"steam":12783250537,"float":0.07018834352493286,"value":405,"item_name":"★ Bowie Knife | Doppler Sapphire (Minimal Wear)"},{"steam":13045513679,"float":0.07397005707025528,"value":337,"item_name":"★ StatTrak™ Bowie Knife | Doppler Sapphire (Minimal Wear)"},{"steam":13112297308,"float":0.0741698294878006,"value":471,"item_name":"★ Bowie Knife | Doppler Sapphire (Minimal Wear)"},{"steam":13121559345,"float":0.07563714683055878,"value":197,"item_name":"★ Bowie Knife | Doppler Sapphire (Minimal Wear)"},{"steam":13121559345,"float":0.07563714683055878,"value":197,"item_name":"★ Bowie Knife | Doppler Sapphire (Minimal Wear)"}]');
        foreach ($patterns as $pattern) {
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
            ->where('client', '=', $username)->get();

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

    public function get_items()
    {
//        echo Carbon::now() . "</br>";
//        $tasks = Task::with('item.paintseeds')->where('site_id', '=', 7)->get();
        echo Carbon::now() . "</br>";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://cs.money/load_bots_inventory');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_exec = curl_exec($curl);

        $items = collect(json_decode($curl_exec));
        echo Carbon::now() . "</br>";
        if (count($items) > 0) { //проверка на то что cs.money вернула предметы
            $items_id = $items->pluck('id.0')->toArray();
            $tasks = Task::with('paintseeds')->where('site_id', '=', 7)->get();

            echo Carbon::now() . "</br>";
            foreach ($tasks as $task) { //перебор задач
                $paintseeds = $task->paintseeds->pluck('steam')->toArray();
                $intersect = array_intersect($paintseeds, $items_id);
                if (count($intersect)) {
                    foreach ($intersect as $item) {
                        $float = $task->item->paintseeds->where('steam', '=', $item)->first()->float;
                        $csmoney_item = $items->where('id.0', '=', $item)->first();
                        $metjm = "https://metjm.net/csgo/#S{$csmoney_item->b[0]}A{$csmoney_item->id[0]}D{$csmoney_item->l[0]}";
                    }
//                        $task->delete();
                }
            }
        }

//        $items_id = $items->pluck('id.0')->toArray();

        dd(Carbon::now());

        dd($items_id);
    }

    public function insert_paintseed_task($site_id)
    {
        set_time_limit(0);
        $tasks = Task::with('item.paintseeds')->where('site_id', '=', $site_id)->get();
        foreach ($tasks as $task) {
            $paintseeds = $task->item->paintseeds;
            if ($task->float) $paintseeds = $paintseeds->where('float', '<=', $task->float);
            if ($task->pattern) $paintseeds = $paintseeds->where('pattern_name', '=', $task->pattern);
            foreach ($paintseeds as $paintseed) {
                DB::insert('insert into paintseed_task (task_id, paintseed_id) values (?, ?)', [$task->id, $paintseed->id]);
            }
        }
        dd(213);
    }

    public function delete_paintseed_task($site_id)
    {
        set_time_limit(0);
        $tasks = Task::where('site_id', '=', $site_id)->get();
        foreach ($tasks as $task) {
            DB::delete('delete from paintseed_task where task_id = ?',[$task->id]);
        }
        dd(213);
    }

}