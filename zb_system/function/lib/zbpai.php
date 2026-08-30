<?php

/**
 * Created by ZBLOGPHP
 * User: zxasd
 * Date: 2026/02/9
 * Time: 17:00.
 */
class ZbpAi
{
    public $text_url;
    public $text_model;
    public $text_apikey;
    public $image_url;
    public $image_model;
    public $image_apikey;
    public $video_url;
    public $video_model;
    public $video_apikey;

    //public $temperature; //0.7;
    //public $max_tokens; //1024;
    //public $thinking;
    public $result;

    public function chat($prompt, $option = [], $return_full = false)
    {
        global $zbp;
        $array = [];
        if (is_null($this->text_url)) {
            $this->text_url = $zbp->option['ZC_TEXT_AI_API_URL'];
        }
        if (is_null($this->text_apikey)) {
            $this->text_apikey = $zbp->option['ZC_TEXT_AI_API_KEY'];
        }
        if (is_null($this->text_model)) {
            $this->text_model = $zbp->option['ZC_TEXT_AI_API_MODEL'];
        }

        $array['model'] = $this->text_model;

        foreach ($option as $key => $value) {
            if ('messages' != $key && !is_null($value)) {
                $array[$key] = $value;
            }
        }

        if (!is_array($prompt)) {
            $array['messages'] = [];
            $array['messages'][] = [
                'role' => 'user',
                'content' => $prompt,
            ];
        } else {
            $array['messages'] = $prompt;
        }

        $this->send($this->text_url, $this->text_apikey, $array);

        if ($return_full) {
            return $this->result;
        }

        return $this->result['choices'][0]['message']['content'] ?? null;
    }

    public function generateImage($prompt, $option = [], $return_full = false)
    {
        global $zbp;
        $array = [];
        if (is_null($this->image_url)) {
            $this->image_url = $zbp->option['ZC_IMAGE_AI_API_URL'];
        }
        if (is_null($this->image_apikey)) {
            $this->image_apikey = $zbp->option['ZC_IMAGE_AI_API_KEY'];
        }
        if (is_null($this->image_model)) {
            $this->image_model = $zbp->option['ZC_IMAGE_AI_API_MODEL'];
        }

        $array['model'] = $this->image_model;

        foreach ($option as $key => $value) {
            if ('prompt' != $key && !is_null($value)) {
                $array[$key] = $value;
            }
        }

        $array['prompt'] = $prompt;

        $this->send($this->image_url, $this->image_apikey, $array);

        if ($return_full) {
            return $this->result;
        }

        return $this->result['data'][0]['url'] ?? null;
    }

    public function generateVideo($prompt, $option = [], $return_full = false)
    {
        global $zbp;
        $array = [];
        if (is_null($this->video_url)) {
            $this->video_url = $zbp->option['ZC_VIDEO_AI_API_URL'];
        }
        if (is_null($this->video_apikey)) {
            $this->video_apikey = $zbp->option['ZC_VIDEO_AI_API_KEY'];
        }
        if (is_null($this->video_model)) {
            $this->video_model = $zbp->option['ZC_VIDEO_AI_API_MODEL'];
        }

        $array['model'] = $this->video_model;

        foreach ($option as $key => $value) {
            if ('prompt' != $key && !is_null($value)) {
                $array[$key] = $value;
            }
        }

        $array['prompt'] = $prompt;

        $this->send($this->video_url, $this->video_apikey, $array);

        if ($return_full) {
            return $this->result;
        }

        $task_id = $this->result['task_id'] ?? null;
        if (!is_null($task_id)) {
            return $task_id;
        }
        $task_id = $this->result['output']['task_id'] ?? null;
        if (!is_null($task_id)) {
            return $task_id;
        }
        $task_id = $this->result['data']['task_id'] ?? null;
        if (!is_null($task_id)) {
            return $task_id;
        }
        $task_id = $this->result['id'] ?? null;
        if (!is_null($task_id)) {
            return $task_id;
        }

        return $this->result;
    }

    public function send($url, $key, $data)
    {
        $ajax = Network::Create();
        $ajax->open('POST', $url);
        $ajax->setTimeOuts(120, 120, 0, 0);
        $ajax->setRequestHeader('Content-Type', 'application/json; charset=utf-8');
        $ajax->setRequestHeader('Authorization', 'Bearer ' . $key);
        $data = json_encode($data);
        $ajax->send($data);

        $json = $ajax->responseText;

        $this->result = json_decode($json, true);
        if (JSON_ERROR_NONE === json_last_error()) {
            return $this->result;
        }
    }
}
