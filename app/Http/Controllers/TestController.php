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
        $patterns = json_decode('[{"steam":12967701877,"float":0.00045749114360660315,"value":721,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12989385109,"float":0.0010249954648315907,"value":945,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13004705079,"float":0.00326270773075521,"value":410,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12824162475,"float":0.0033137111458927393,"value":119,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12467164015,"float":0.0033825288992375135,"value":552,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":11967293417,"float":0.005255400203168392,"value":949,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12763500314,"float":0.007214424666017294,"value":766,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":11760754892,"float":0.007509664166718721,"value":970,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":11378340923,"float":0.007512131705880165,"value":41,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12991674421,"float":0.007726198993623257,"value":958,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13082223770,"float":0.007735523395240307,"value":397,"item_name":"★ StatTrak™ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12908952096,"float":0.007799633778631687,"value":651,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":11114666265,"float":0.007888459600508213,"value":467,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12204469353,"float":0.007956272922456264,"value":902,"item_name":"★ StatTrak™ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":11241731656,"float":0.007996953092515469,"value":293,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12764741570,"float":0.00813989993184805,"value":361,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12353925288,"float":0.00819050706923008,"value":713,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12214515140,"float":0.008203122764825821,"value":914,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12506996040,"float":0.00823891256004572,"value":224,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12654750884,"float":0.008339081890881062,"value":840,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12873671448,"float":0.008402964100241661,"value":71,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12035516083,"float":0.008412637747824192,"value":382,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12835550903,"float":0.00852859579026699,"value":903,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12689906891,"float":0.008664953522384167,"value":776,"item_name":"★ StatTrak™ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12323201056,"float":0.009349769912660122,"value":628,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12782615506,"float":0.009456914849579334,"value":75,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":10794348652,"float":0.009481457062065601,"value":941,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":11554473048,"float":0.00965998973697424,"value":355,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12942572680,"float":0.00981551967561245,"value":46,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12948414372,"float":0.009916705079376698,"value":959,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12194121507,"float":0.010000401176512241,"value":871,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12189980857,"float":0.010023579001426697,"value":505,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12828068736,"float":0.010458653792738914,"value":477,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":11162017042,"float":0.010639090090990067,"value":625,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13042523740,"float":0.010667997412383556,"value":744,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":10244916903,"float":0.010973463766276836,"value":878,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12728119272,"float":0.011149590834975243,"value":719,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":10911448704,"float":0.01137122604995966,"value":614,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12032655679,"float":0.011635564267635345,"value":251,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12630178994,"float":0.01185216847807169,"value":999,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12275854855,"float":0.014512719586491585,"value":683,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":11540180142,"float":0.015245988965034485,"value":96,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12325387562,"float":0.015721751376986504,"value":414,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13093087805,"float":0.01605089195072651,"value":554,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12676730528,"float":0.01698988862335682,"value":54,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12847384073,"float":0.01712515577673912,"value":738,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12999614654,"float":0.01785174570977688,"value":741,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":10779139871,"float":0.018374377861618996,"value":504,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13086402832,"float":0.01881466433405876,"value":511,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":10284064200,"float":0.019027167931199074,"value":685,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12892823460,"float":0.019358357414603233,"value":528,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12483299080,"float":0.019864942878484726,"value":499,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":10307306822,"float":0.020047087222337723,"value":943,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13078595371,"float":0.020383358001708984,"value":614,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":11960377214,"float":0.021158134564757347,"value":438,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13095446085,"float":0.02147875539958477,"value":141,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12380105802,"float":0.021480049937963486,"value":210,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":11340635824,"float":0.022184152156114578,"value":38,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12314497649,"float":0.02242119237780571,"value":424,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":11319332451,"float":0.022510115057229996,"value":332,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13001042544,"float":0.022991403937339783,"value":106,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12440814183,"float":0.023175209760665894,"value":832,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13098093148,"float":0.02322831191122532,"value":639,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12997306892,"float":0.02328779734671116,"value":960,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":11954415812,"float":0.02369210496544838,"value":551,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12811750904,"float":0.024433640763163567,"value":526,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12934549554,"float":0.024702100083231926,"value":909,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":11386813347,"float":0.024709029123187065,"value":176,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12955744264,"float":0.024829238653182983,"value":370,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12885440159,"float":0.024865902960300446,"value":326,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12160064961,"float":0.02536890283226967,"value":953,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":10311716730,"float":0.025870703160762787,"value":121,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13060194939,"float":0.02697099559009075,"value":49,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13105876375,"float":0.026984404772520065,"value":619,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12926730697,"float":0.027461346238851547,"value":594,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12191716412,"float":0.027961134910583496,"value":15,"item_name":"★ StatTrak™ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13089561781,"float":0.028267234563827515,"value":850,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12799596488,"float":0.028782930225133896,"value":103,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12883607945,"float":0.029065456241369247,"value":633,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13109090786,"float":0.030128782615065575,"value":55,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":11606030531,"float":0.030162649229168892,"value":659,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12742717585,"float":0.03017764538526535,"value":446,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12971722650,"float":0.030271172523498535,"value":91,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":10858452988,"float":0.03036591410636902,"value":537,"item_name":"★ StatTrak™ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13021533587,"float":0.031237922608852386,"value":329,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":10521081746,"float":0.03131033852696419,"value":385,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12276620574,"float":0.031348343938589096,"value":486,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13008766582,"float":0.031386133283376694,"value":388,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12806236355,"float":0.03183832764625549,"value":343,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12781032365,"float":0.032064102590084076,"value":415,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":11368405236,"float":0.03209264948964119,"value":943,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":11575891190,"float":0.03219003975391388,"value":380,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":11707150951,"float":0.032251253724098206,"value":838,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12932520196,"float":0.03230002522468567,"value":218,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12982568156,"float":0.032355356961488724,"value":311,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12547785662,"float":0.03238745406270027,"value":733,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12882972901,"float":0.03253844752907753,"value":798,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12412388969,"float":0.03263048082590103,"value":389,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12841135931,"float":0.03268374502658844,"value":882,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12985869275,"float":0.03274848684668541,"value":372,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12985869275,"float":0.03274848684668541,"value":372,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12867281219,"float":0.03282608836889267,"value":782,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12966389907,"float":0.0329756960272789,"value":693,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13033603519,"float":0.03312564641237259,"value":120,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":10244559575,"float":0.03319145366549492,"value":370,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12804663666,"float":0.0333046019077301,"value":727,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12834760290,"float":0.033381152898073196,"value":459,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12648015485,"float":0.03347587212920189,"value":629,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13073285739,"float":0.0334896557033062,"value":182,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12599951435,"float":0.033552270382642746,"value":973,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12860407899,"float":0.03364526107907295,"value":643,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12700712730,"float":0.03395368158817291,"value":41,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12651018372,"float":0.034334152936935425,"value":269,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12551047167,"float":0.03438417240977287,"value":544,"item_name":"★ StatTrak™ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13023416169,"float":0.03440866246819496,"value":721,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12529463947,"float":0.03452296555042267,"value":551,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13102989159,"float":0.03457847237586975,"value":462,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12780997843,"float":0.03483607620000839,"value":926,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12938133491,"float":0.034921541810035706,"value":254,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13109194085,"float":0.034969307482242584,"value":518,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13017016904,"float":0.03527017682790756,"value":419,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12800782274,"float":0.03554403781890869,"value":36,"item_name":"★ StatTrak™ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12938133559,"float":0.035705018788576126,"value":458,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":10749521536,"float":0.03590470179915428,"value":724,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12499233229,"float":0.03595505654811859,"value":255,"item_name":"★ StatTrak™ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12641385487,"float":0.03774334862828255,"value":37,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":11689568338,"float":0.040602587163448334,"value":576,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12926771385,"float":0.041950397193431854,"value":663,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":10526742838,"float":0.04713449254631996,"value":645,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13008385592,"float":0.050038713961839676,"value":627,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13060238375,"float":0.05052029341459274,"value":715,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":11487081420,"float":0.053048133850097656,"value":174,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12842789721,"float":0.05333253741264343,"value":689,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12919764768,"float":0.05340364947915077,"value":56,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12761357667,"float":0.057933349162340164,"value":806,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12982746881,"float":0.05985720083117485,"value":423,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12486153333,"float":0.06235021352767944,"value":820,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":11152180138,"float":0.06290119141340256,"value":934,"item_name":"★ StatTrak™ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":13059356868,"float":0.06308288127183914,"value":717,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12815741706,"float":0.06509356200695038,"value":657,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":10835410727,"float":0.0671318918466568,"value":865,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":10818927965,"float":0.06829550862312317,"value":806,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12475899351,"float":0.0683901235461235,"value":38,"item_name":"★ Butterfly Knife | Doppler Ruby (Factory New)"},{"steam":12175265159,"float":0.07530830800533295,"value":106,"item_name":"★ Butterfly Knife | Doppler Ruby (Minimal Wear)"},{"steam":11986505034,"float":0.07715685665607452,"value":427,"item_name":"★ Butterfly Knife | Doppler Ruby (Minimal Wear)"},{"steam":11986505034,"float":0.07715685665607452,"value":427,"item_name":"★ Butterfly Knife | Doppler Ruby (Minimal Wear)"}]');
        foreach ($patterns as $pattern){
            $item_id = Item::where('name', '=', $pattern->item_name)->first();
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