<?php
namespace SMBR\Http\Controllers;

use Illuminate\Http\Request;
use SMBR\Logger;
use SMBR\Randomizer;
use SMBR\Rom;

class RandomizerController extends Controller
{
    public function generate(Request $request)
    {
        $log = null;

        // Save base rom as temp file
        $tmpfilename = tempnam(sys_get_temp_dir(), "SMBR");
        $r = $request->input('rom');
        $romdata = pack('C*', ...$r);
        $tmpfile = fopen($tmpfilename, "w+");
        fwrite($tmpfile, $romdata);
        fclose($tmpfile);

        $rom = new Rom($tmpfilename);

        // TODO: set options here
        $seed = (int) $request->input('seed');
        $options['pipeTransitions'] = $request->input('pipeTransitions');
        $options['shuffleLevels'] = $request->input('shuffleLevels');
        $options['normalWorldLength'] = $request->input('normalWorldLength');
        $options['enemies'] = $request->input('enemies');
        $options['blocks'] = $request->input('blocks');
        $options['bowserAbilities'] = $request->input('bowserAbilities');
        $options['bowserHitpoints'] = $request->input('bowserHitpoints');
        $options['startingLives'] = $request->input('startingLives');
        $options['warpZones'] = $request->input('warpZones');
        $options['hiddenWarpDestinations'] = $request->input('hiddenWarpDestinations');
        $options['fireworks'] = $request->input('fireworks');
        $options['shuffleUndergroundBonus'] = $request->input('shuffleUndergroundBonus');
        $options['randomizeBackground'] = $request->input('randomizeBackground');
        $options['hardMode'] = $request->input('hardMode');
        $options['randomizeUndergroundBricks'] = $request->input('randomizeUndergroundBricks');

        $options['mariocolors'] = $request->input('mario');
        $options['luigicolors'] = $request->input('luigi');
        $options['firecolors'] = $request->input('fire');

        // if seed == null a random seed will be chosen, else it will use the user's chosen seed.
        $rando = new Randomizer($seed, $options, $rom);

        $rando->setSeed($rando->getSeed());
        $rando->makeFlags();

        $romfilename = $request->input('romfilename');

        // Set filenames
        $outfilename = "output/roms/" . substr($romfilename, 0, -4) . "_" . $rando->getSeed() . "-" . $rando->flags . ".nes";
        $logfilename = "output/logs/" . substr($romfilename, 0, -4) . "_" . $rando->getSeed() . "-" . $rando->flags . ".log.txt";
        $outfilebasename = substr($romfilename, 0, -4) . "_" . $rando->getSeed() . "-" . $rando->flags . ".nes";

        // Start the logger
        $log = new Logger($logfilename, $request->input('generateLog'), $request->input('verboseLog') == "true" ? "verbose" : "normal");
        $rom->setLogger($log);
        $rando->setLogger($log);

        // Make seedhash
        $rando->makeSeedHash();

        // Print out the selected options and relevant information
        // $rando->printOptions();

        // Make the seed a.k.a. this performs the actual randomization!!!!
        $randomized_game = $rando->makeSeed();

        // Write all changes (to temporary file)
        $rom->writeGame($randomized_game);

        // Save the new ROM file
        $rom->save($outfilename);

        // write JSON formatted data to logfile
        // $game_json = json_encode($randomized_game, JSON_PRETTY_PRINT);
        // $log->write("\nJSON:\n\n");
        // $log->write($game_json);
        // $log->write("\n\n");

        $log->close();

        $base64data = $rom->b64();

        $responseData = [
            'fullpath' => $outfilename,
            'filename' => $outfilebasename,
            'logfullpath' => $logfilename,
            'base64data' => $base64data,
        ];

        return json_encode($responseData);
    }

    public function options()
    {
        return config('smbr.randomizer.options');
    }

    public function defaultoptions()
    {
        return config('smbr.randomizer.defaultOptions');
    }

    public function getflags(Request $request)
    {
        $options['pipeTransitions'] = $request->input('pipeTransitions');
        $options['shuffleLevels'] = $request->input('shuffleLevels');
        $options['normalWorldLength'] = $request->input('normalWorldLength');
        $options['enemies'] = $request->input('enemies');
        $options['blocks'] = $request->input('blocks');
        $options['bowserAbilities'] = $request->input('bowserAbilities');
        $options['bowserHitpoints'] = $request->input('bowserHitpoints');
        $options['startingLives'] = $request->input('startingLives');
        $options['warpZones'] = $request->input('warpZones');
        $options['hiddenWarpDestinations'] = $request->input('hiddenWarpDestinations');
        $options['fireworks'] = $request->input('fireworks');
        $options['shuffleUndergroundBonus'] = $request->input('shuffleUndergroundBonus');
        $options['randomizeBackground'] = $request->input('randomizeBackground');
        $options['hardMode'] = $request->input('hardMode');
        $options['randomizeUndergroundBricks'] = $request->input('randomizeUndergroundBricks');

        $rando = new Randomizer(0, $options, null);
        return $rando->calculateFlags($options);
    }

    public function setOptionsFromFlagstring(Request $request)
    {
        $rando = new Randomizer(0);
        $rando->setOptionsFromFlagstring($request->input('flagstring'));
        return $rando->getOptions();
    }
}
