<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Nesk\Puphpeteer\Puppeteer;
use Nesk\Rialto\Data\JsFunction;
use Nesk\Rialto\Exceptions\Node;
use App\Models\HtmlElement;
use Purifier;

class Scrapper implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $target;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($target) {
        $this->target = $target;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        
        $puppeteer = new Puppeteer();
        $browser = $puppeteer->launch();
        $page = $browser->newPage();
        
        try {            
            $page->tryCatch->goto($this->target['url']);
            $element = $page->evaluate(JsFunction::createWithBody("
                return document.querySelector('.{$this->target['selector']}').outerHTML
            "));
        } catch (Node\Exception $exception) {
            return false;
        }

        $html_element = new HtmlElement();
        $html_element->site = $this->target['url'];
        $html_element->selector = $this->target['selector'];
        $html_element->html = Purifier::clean($element);

        $html_element->save();

        $browser->close();
    }
}
