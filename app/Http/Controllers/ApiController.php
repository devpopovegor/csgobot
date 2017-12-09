<?php

namespace App\Http\Controllers;

use App\Item;
use App\Paintseed;
use App\Pattern;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class ApiController extends Controller
{
    public function addItem()
    {
        $name = $_GET['name'];
        $phase = $_GET['phase'] ? $_GET['phase'] : '';
        $float = $_GET['float'];
        $pattern = $_GET['pattern'];
        $item = Item::where('name', '=', $name)->where('phase', '=', $phase)->first();
        if ($item){
            if (Task::where('item_id', '=', $item->id)->where('client','=','ska4an')
                ->where('site_id', '=', 7)->where('float', '=', $float)->where('pattern', '=', $pattern)->first()){
                return "Search already exists";
            }
        }
        else {
            return "Item not exists";
        }

        Task::create(['item_id' => $item->id, 'site_id' => 7, 'float' => $float, 'client' => 'ska4an', 'pattern' => $pattern, 'chat_id' => '']);
        return "OK";
    }

    public function getList()
    {
        $tasks = json_encode(Task::with('item')->where('site_id', '=', '7')
            ->where('client','=', 'ska4an')->get());

        return json_encode($tasks);
    }

    public function getPatterns()
    {
        return json_encode(Pattern::all());
    }

    public function send()
    {
    	$item = Item::find($_GET['item_id']);
	    Telegram::sendMessage([
		    'chat_id' => 222881167,
		    'text' => "Бот для cs.money отправил оффер на {$item->full_name}",
		    'parse_mode' => 'HTML'
	    ]);

	    return json_encode('ok');
    }

    public function setPatterns()
    {
        set_time_limit(0);
        $patterns = json_decode('[{"paintseed":"555","item":"6315121215"},{"paintseed":"555","item":"4546274431"},{"paintseed":"555","item":"12508982617"},{"paintseed":"555","item":"7919671413"},{"paintseed":"555","item":"7411662177"},{"paintseed":"555","item":"10290007406"},{"paintseed":"555","item":"9276929720"},{"paintseed":"592","item":"12321354022"},{"paintseed":"592","item":"12206687672"},{"paintseed":"592","item":"12624375537"},{"paintseed":"592","item":"2733336506"},{"paintseed":"670","item":"5358197113"},{"paintseed":"670","item":"11884841840"},{"paintseed":"670","item":"11551500881"},{"paintseed":"670","item":"7565605092"},{"paintseed":"670","item":"12880782145"},{"paintseed":"670","item":"10737136343"},{"paintseed":"661","item":"12106674436"},{"paintseed":"661","item":"12272265439"},{"paintseed":"661","item":"8633961966"},{"paintseed":"955","item":"6789325641"},{"paintseed":"955","item":"9942676452"},{"paintseed":"179","item":"11265153890"},{"paintseed":"179","item":"11091454825"},{"paintseed":"151","item":"11461208002"},{"paintseed":"151","item":"12508263073"},{"paintseed":"151","item":"9358392086"},{"paintseed":"321","item":"7527334185"},{"paintseed":"321","item":"3661411383"},{"paintseed":"321","item":"8909832407"},{"paintseed":"321","item":"6685549349"},{"paintseed":"321","item":"10106303861"},{"paintseed":"228","item":"8549608212"},{"paintseed":"228","item":"4498551379"},{"paintseed":"228","item":"12047284176"},{"paintseed":"228","item":"12640676727"},{"paintseed":"695","item":"6033304833"},{"paintseed":"695","item":"9033720819"},{"paintseed":"695","item":"10036392067"},{"paintseed":"695","item":"12507811974"},{"paintseed":"695","item":"10170247966"},{"paintseed":"695","item":"12286886804"},{"paintseed":"695","item":"10425987108"},{"paintseed":"695","item":"12804817123"},{"paintseed":"695","item":"7082642012"},{"paintseed":"695","item":"10768674957"},{"paintseed":"695","item":"5927445715"},{"paintseed":"695","item":"11609323133"},{"paintseed":"760","item":"10238055222"},{"paintseed":"760","item":"12855385258"},{"paintseed":"760","item":"12306669135"},{"paintseed":"760","item":"12012158081"},{"paintseed":"760","item":"10361454886"},{"paintseed":"760","item":"11832963821"},{"paintseed":"828","item":"12823364820"},{"paintseed":"828","item":"8841831966"},{"paintseed":"828","item":"12883270377"},{"paintseed":"617","item":"9229110434"},{"paintseed":"617","item":"6367195198"},{"paintseed":"617","item":"8660284562"},{"paintseed":"617","item":"12824725983"},{"paintseed":"617","item":"4703226778"},{"paintseed":"617","item":"12544161161"},{"paintseed":"969","item":"3956112081"},{"paintseed":"969","item":"11176378447"},{"paintseed":"969","item":"7754412417"},{"paintseed":"922","item":"1744189601"},{"paintseed":"905","item":"12791214542"},{"paintseed":"905","item":"5338574533"},{"paintseed":"905","item":"7510200270"},{"paintseed":"905","item":"12172156595"},{"paintseed":"791","item":"12761599460"},{"paintseed":"791","item":"12754953068"},{"paintseed":"791","item":"9548256217"},{"paintseed":"791","item":"4573053900"},{"paintseed":"312","item":"12628524397"},{"paintseed":"312","item":"12055001863"},{"paintseed":"312","item":"6211377540"},{"paintseed":"312","item":"12169529527"},{"paintseed":"312","item":"7828629110"},{"paintseed":"387","item":"11379931137"},{"paintseed":"387","item":"9327733193"},{"paintseed":"387","item":"12907073429"},{"paintseed":"387","item":"12468163181"},{"paintseed":"387","item":"11273005968"},{"paintseed":"892","item":"8843065014"},{"paintseed":"512","item":"11415232646"},{"paintseed":"512","item":"9535045999"},{"paintseed":"512","item":"11549146923"},{"paintseed":"512","item":"10107421421"},{"paintseed":"512","item":"11829454555"},{"paintseed":"512","item":"12112973636"},{"paintseed":"604","item":"12558489182"},{"paintseed":"604","item":"11616673773"},{"paintseed":"604","item":"10614078151"},{"paintseed":"604","item":"12410093250"},{"paintseed":"604","item":"8236761808"},{"paintseed":"604","item":"9349250241"},{"paintseed":"604","item":"9335779056"},{"paintseed":"479","item":"12923728961"},{"paintseed":"479","item":"4646302667"},{"paintseed":"479","item":"12903675989"},{"paintseed":"479","item":"11369725961"},{"paintseed":"202","item":"12719109917"},{"paintseed":"202","item":"12241509015"},{"paintseed":"868","item":"10534839267"},{"paintseed":"868","item":"2726290441"},{"paintseed":"868","item":"2963241969"},{"paintseed":"868","item":"5053433784"},{"paintseed":"352","item":"2928818650"},{"paintseed":"352","item":"6902074940"},{"paintseed":"352","item":"6765804739"},{"paintseed":"74","item":"9801945237"},{"paintseed":"74","item":"12685644585"},{"paintseed":"74","item":"9028382075"},{"paintseed":"74","item":"9966077389"},{"paintseed":"74","item":"5209026786"},{"paintseed":"74","item":"3740211537"},{"paintseed":"996","item":"8959775056"},{"paintseed":"996","item":"11563308375"},{"paintseed":"996","item":"12812486295"},{"paintseed":"163","item":"12825731132"},{"paintseed":"163","item":"7709746692"},{"paintseed":"163","item":"6205251583"},{"paintseed":"259","item":"6981320749"},{"paintseed":"259","item":"9529884169"},{"paintseed":"259","item":"8236271773"},{"paintseed":"259","item":"1739578191"},{"paintseed":"325","item":"8621838713"},{"paintseed":"325","item":"3552072340"},{"paintseed":"325","item":"386548891"},{"paintseed":"325","item":"1938888831"},{"paintseed":"605","item":"12431514785"},{"paintseed":"605","item":"11206880161"},{"paintseed":"605","item":"2571000305"},{"paintseed":"605","item":"7876326808"},{"paintseed":"844","item":"8097207052"},{"paintseed":"844","item":"12544074726"},{"paintseed":"207","item":"6075621252"},{"paintseed":"207","item":"8131969518"},{"paintseed":"207","item":"9506582520"},{"paintseed":"207","item":"10804145785"},{"paintseed":"207","item":"12545393837"},{"paintseed":"28","item":"4793128758"},{"paintseed":"28","item":"12881928424"},{"paintseed":"28","item":"12812486588"},{"paintseed":"28","item":"10752612627"},{"paintseed":"209","item":"600122662"},{"paintseed":"209","item":"11419007083"},{"paintseed":"520","item":"11340652622"},{"paintseed":"520","item":"6710240880"},{"paintseed":"520","item":"12721395883"},{"paintseed":"520","item":"12457399552"},{"paintseed":"112","item":"8530471970"},{"paintseed":"112","item":"9553485159"},{"paintseed":"112","item":"10013272625"},{"paintseed":"112","item":"2976889077"},{"paintseed":"112","item":"12557268802"},{"paintseed":"112","item":"12937612526"},{"paintseed":"103","item":"12857999478"},{"paintseed":"103","item":"11955777417"},{"paintseed":"103","item":"3775823582"},{"paintseed":"103","item":"3030388815"},{"paintseed":"103","item":"12903634447"},{"paintseed":"644","item":"10972109467"},{"paintseed":"644","item":"4113066310"},{"paintseed":"644","item":"8472006482"},{"paintseed":"644","item":"12926790607"},{"paintseed":"644","item":"5679326329"},{"paintseed":"644","item":"12930773599"},{"paintseed":"381","item":"8792873814"},{"paintseed":"381","item":"803067561"},{"paintseed":"381","item":"6778941545"},{"paintseed":"894","item":"10883219805"},{"paintseed":"894","item":"7328173412"},{"paintseed":"894","item":"11971961652"},{"paintseed":"894","item":"10134067498"},{"paintseed":"888","item":"9533192103"},{"paintseed":"888","item":"6975568252"},{"paintseed":"690","item":"8940590523"},{"paintseed":"690","item":"2220530394"},{"paintseed":"809","item":"12719110206"},{"paintseed":"713","item":"11610141026"},{"paintseed":"713","item":"12635217007"},{"paintseed":"713","item":"8964931372"},{"paintseed":"713","item":"295570268"},{"paintseed":"713","item":"10419196267"},{"paintseed":"713","item":"11612499603"},{"paintseed":"713","item":"8323365058"},{"paintseed":"713","item":"8897853747"},{"paintseed":"887","item":"12773893090"},{"paintseed":"887","item":"10361086204"},{"paintseed":"887","item":"12838731151"},{"paintseed":"887","item":"12409272487"},{"paintseed":"887","item":"8886848144"},{"paintseed":"82","item":"10298016633"},{"paintseed":"82","item":"11393137566"},{"paintseed":"82","item":"10121057595"},{"paintseed":"82","item":"8156941729"},{"paintseed":"532","item":"10508579302"},{"paintseed":"532","item":"8944734742"},{"paintseed":"532","item":"11960065433"},{"paintseed":"532","item":"10586679631"},{"paintseed":"532","item":"9628975716"},{"paintseed":"532","item":"9279816328"},{"paintseed":"532","item":"12484884640"},{"paintseed":"278","item":"11980076497"},{"paintseed":"278","item":"10404515073"},{"paintseed":"278","item":"9173869239"},{"paintseed":"278","item":"7016278707"},{"paintseed":"278","item":"8849525622"},{"paintseed":"278","item":"11843178997"},{"paintseed":"278","item":"11695948524"},{"paintseed":"278","item":"12273174476"},{"paintseed":"278","item":"5453526450"},{"paintseed":"278","item":"2362028275"},{"paintseed":"750","item":"12866646541"},{"paintseed":"750","item":"7317892008"},{"paintseed":"93","item":"10191721871"},{"paintseed":"93","item":"2034439207"},{"paintseed":"147","item":"5664577724"},{"paintseed":"147","item":"12511436248"},{"paintseed":"147","item":"1266964386"},{"paintseed":"147","item":"9256411912"},{"paintseed":"526","item":"1955676578"},{"paintseed":"526","item":"12928757942"},{"paintseed":"526","item":"9484847964"},{"paintseed":"526","item":"1773443254"},{"paintseed":"526","item":"4220171552"},{"paintseed":"375","item":"2451993388"},{"paintseed":"375","item":"10105718293"},{"paintseed":"430","item":"11677362909"},{"paintseed":"430","item":"11303962908"},{"paintseed":"430","item":"6908910762"},{"paintseed":"189","item":"11710559857"},{"paintseed":"189","item":"4754194500"},{"paintseed":"189","item":"8848914776"},{"paintseed":"189","item":"10054294598"},{"paintseed":"189","item":"8334959294"},{"paintseed":"189","item":"12902207042"},{"paintseed":"189","item":"9915748103"},{"paintseed":"122","item":"12203026523"},{"paintseed":"122","item":"12438274735"},{"paintseed":"847","item":"6072625307"},{"paintseed":"847","item":"7008155794"},{"paintseed":"847","item":"6789522339"},{"paintseed":"627","item":"5742605045"},{"paintseed":"627","item":"11210615854"},{"paintseed":"627","item":"12843552042"},{"paintseed":"627","item":"6770975405"},{"paintseed":"627","item":"8258152083"},{"paintseed":"627","item":"4215698423"},{"paintseed":"627","item":"12354351060"},{"paintseed":"872","item":"6016054246"},{"paintseed":"872","item":"12625295662"},{"paintseed":"872","item":"3518087359"},{"paintseed":"872","item":"10504596975"},{"paintseed":"872","item":"12803786227"},{"paintseed":"872","item":"8512896384"},{"paintseed":"363","item":"9965122435"},{"paintseed":"363","item":"12252012531"},{"paintseed":"363","item":"11653715047"},{"paintseed":"363","item":"10892921788"},{"paintseed":"363","item":"11768507934"},{"paintseed":"363","item":"5063561484"},{"paintseed":"9","item":"12715720519"},{"paintseed":"9","item":"7827641999"},{"paintseed":"9","item":"12888794098"},{"paintseed":"647","item":"2199912007"},{"paintseed":"647","item":"2308845547"},{"paintseed":"647","item":"10439470098"},{"paintseed":"647","item":"5961404622"},{"paintseed":"980","item":"11366967304"},{"paintseed":"980","item":"10513936019"},{"paintseed":"980","item":"11314322352"},{"paintseed":"980","item":"3902659050"},{"paintseed":"980","item":"9991391457"},{"paintseed":"980","item":"12288871605"},{"paintseed":"227","item":"10508751309"},{"paintseed":"227","item":"12707089506"},{"paintseed":"227","item":"11710559964"},{"paintseed":"227","item":"9075930849"},{"paintseed":"227","item":"12765949354"},{"paintseed":"227","item":"12586856366"},{"paintseed":"227","item":"12108476460"},{"paintseed":"823","item":"9224954633"},{"paintseed":"823","item":"9980696838"},{"paintseed":"823","item":"8843331213"},{"paintseed":"823","item":"4771656635"},{"paintseed":"493","item":"6283644119"},{"paintseed":"493","item":"4420418031"},{"paintseed":"754","item":"3883604111"},{"paintseed":"754","item":"12769550599"},{"paintseed":"235","item":"6600879253"},{"paintseed":"235","item":"8850025958"},{"paintseed":"235","item":"4742612572"},{"paintseed":"235","item":"10100247683"},{"paintseed":"463","item":"10658855671"},{"paintseed":"463","item":"4147161178"},{"paintseed":"463","item":"9239602595"},{"paintseed":"463","item":"11130852080"},{"paintseed":"481","item":"12845861391"},{"paintseed":"481","item":"12388757263"},{"paintseed":"481","item":"11968896257"},{"paintseed":"481","item":"12114700066"},{"paintseed":"481","item":"4656569007"},{"paintseed":"481","item":"4062436150"},{"paintseed":"236","item":"5242026240"},{"paintseed":"236","item":"2223835477"},{"paintseed":"236","item":"5903409817"},{"paintseed":"236","item":"4924189997"},{"paintseed":"236","item":"10183307606"},{"paintseed":"236","item":"12888003997"},{"paintseed":"236","item":"10661676010"},{"paintseed":"456","item":"11542851061"},{"paintseed":"456","item":"3196386211"},{"paintseed":"456","item":"2635363209"},{"paintseed":"456","item":"12411753581"},{"paintseed":"525","item":"12771701520"},{"paintseed":"525","item":"9951540352"},{"paintseed":"525","item":"8836119286"},{"paintseed":"525","item":"7478551770"},{"paintseed":"525","item":"6835583563"},{"paintseed":"878","item":"10417051356"},{"paintseed":"878","item":"12771153945"},{"paintseed":"878","item":"12238209724"},{"paintseed":"878","item":"3146655787"},{"paintseed":"878","item":"11076212506"},{"paintseed":"849","item":"3538267859"},{"paintseed":"849","item":"9017122967"},{"paintseed":"849","item":"12599017154"},{"paintseed":"137","item":"7881929077"},{"paintseed":"137","item":"11773416432"},{"paintseed":"137","item":"12770524431"},{"paintseed":"137","item":"12663179651"},{"paintseed":"137","item":"9499284864"},{"paintseed":"137","item":"11679891090"},{"paintseed":"137","item":"11317524515"},{"paintseed":"137","item":"12400510875"},{"paintseed":"284","item":"5368522111"},{"paintseed":"284","item":"12512985608"},{"paintseed":"284","item":"12740623385"},{"paintseed":"284","item":"11464298553"},{"paintseed":"284","item":"8306997969"},{"paintseed":"48","item":"12763010713"},{"paintseed":"48","item":"11710560425"},{"paintseed":"385","item":"12847044425"},{"paintseed":"385","item":"10327415467"},{"paintseed":"385","item":"10324464564"},{"paintseed":"240","item":"7421400897"},{"paintseed":"240","item":"12771037227"},{"paintseed":"240","item":"12533329550"},{"paintseed":"921","item":"4213221338"},{"paintseed":"921","item":"11224012757"},{"paintseed":"921","item":"2663840104"},{"paintseed":"921","item":"8796531121"},{"paintseed":"396","item":"2796473843"},{"paintseed":"396","item":"12470558234"},{"paintseed":"396","item":"8225806299"},{"paintseed":"396","item":"2604596514"},{"paintseed":"396","item":"12838333094"},{"paintseed":"396","item":"8714961165"},{"paintseed":"396","item":"5606018050"},{"paintseed":"537","item":"11507907576"},{"paintseed":"537","item":"8779519580"},{"paintseed":"537","item":"12376902721"},{"paintseed":"537","item":"9201135071"},{"paintseed":"537","item":"5712112074"},{"paintseed":"537","item":"11462161497"},{"paintseed":"537","item":"11624427364"},{"paintseed":"65","item":"5000778595"},{"paintseed":"65","item":"10340154674"},{"paintseed":"65","item":"10961477036"},{"paintseed":"65","item":"7294569330"},{"paintseed":"65","item":"10175824661"},{"paintseed":"429","item":"7115448955"},{"paintseed":"429","item":"12401593216"},{"paintseed":"429","item":"12902668963"},{"paintseed":"689","item":"10242442275"},{"paintseed":"689","item":"7143772680"},{"paintseed":"689","item":"7223383105"},{"paintseed":"689","item":"12905205923"},{"paintseed":"689","item":"11493653471"},{"paintseed":"689","item":"7011174335"},{"paintseed":"689","item":"7267428313"},{"paintseed":"689","item":"12896749354"},{"paintseed":"689","item":"8105511085"},{"paintseed":"310","item":"12870521761"},{"paintseed":"310","item":"4806268595"},{"paintseed":"310","item":"5921506376"},{"paintseed":"770","item":"7898676278"},{"paintseed":"770","item":"10943596732"},{"paintseed":"770","item":"9743229808"},{"paintseed":"770","item":"7124868140"},{"paintseed":"770","item":"10351994753"},{"paintseed":"450","item":"12935297699"},{"paintseed":"450","item":"4053132502"},{"paintseed":"450","item":"5130386656"},{"paintseed":"450","item":"11914924324"},{"paintseed":"450","item":"10501485184"},{"paintseed":"450","item":"11770975532"},{"paintseed":"169","item":"6309729595"},{"paintseed":"169","item":"8763500795"},{"paintseed":"169","item":"10479575246"},{"paintseed":"169","item":"8947132383"},{"paintseed":"4","item":"9195363566"},{"paintseed":"4","item":"12577032788"},{"paintseed":"4","item":"11565381619"},{"paintseed":"4","item":"3248092329"},{"paintseed":"4","item":"12932233422"},{"paintseed":"566","item":"12900153065"},{"paintseed":"566","item":"10143051110"},{"paintseed":"566","item":"9636177452"},{"paintseed":"566","item":"12714571340"},{"paintseed":"566","item":"8453102961"},{"paintseed":"566","item":"10206874439"},{"paintseed":"566","item":"3692293859"},{"paintseed":"490","item":"9516644977"},{"paintseed":"490","item":"6777989643"},{"paintseed":"490","item":"4016309361"},{"paintseed":"490","item":"11941074895"},{"paintseed":"173","item":"12894261698"},{"paintseed":"970","item":"11177534448"},{"paintseed":"970","item":"12305607922"},{"paintseed":"970","item":"4282458700"},{"paintseed":"970","item":"11911951931"},{"paintseed":"970","item":"12821275273"},{"paintseed":"200","item":"5694947304"},{"paintseed":"200","item":"2735754162"},{"paintseed":"200","item":"5996069590"},{"paintseed":"200","item":"9993551859"},{"paintseed":"200","item":"3061515416"},{"paintseed":"467","item":"11726061041"},{"paintseed":"467","item":"9994224968"},{"paintseed":"467","item":"12926000196"},{"paintseed":"467","item":"12838727091"},{"paintseed":"632","item":"8977177055"},{"paintseed":"632","item":"4892235055"},{"paintseed":"632","item":"12838733866"}]');
        foreach ($patterns as $pattern){
                Paintseed::create(['item_id' => $pattern->item, 'value' => $pattern->paintseed]);
        }

        return 'OK';
    }

    public function getSteam()
    {
        $tasks = Task::with('item')->where('pattern', '!=', null)->where('client', '=', 'ska4an')
            ->where('site_id', '=', '7')->get();

        $paintseeds = [];
        foreach ($tasks as $task){
            $paterns = $task->item->patterns->where('name', '=', $task->pattern)->toArray();
            foreach ($paterns as $patern){
                $paintseeds[] = $patern['value'];

            }
        }
        $steam_ids = DB::table('paintseeds')->whereIn('value',$paintseeds)->distinct()->pluck('item_id')->toArray();

        return json_encode($steam_ids);

    }
}
