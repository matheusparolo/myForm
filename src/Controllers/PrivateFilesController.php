<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Controllers;

class PrivateFilesController
{

    public function answerAudio($request, $response, $args)
    {

        try{

            $file = $_SERVER["DOCUMENT_ROOT"] . '/../private/assets/audio/form_' . $args["formID"] . "/interviewee_" . $args["intervieweeID"] . "/question_" . $args["questionID"] . ".ogg";
            $fh = fopen($file, 'rb');

            $stream = new \Slim\Http\Stream($fh);

            return $response
                ->withHeader('Content-Type', 'audio/ogg')
                ->withHeader('Content-Description', 'File Transfer')
                ->withHeader('Content-Transfer-Encoding', 'binary')
                ->withHeader('Expires', '0')
                ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                ->withHeader('Pragma', 'public')
                ->withBody($stream);

        }catch(\Exception $e){

            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
            exit;

        }


    }


}