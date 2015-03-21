<?php

/*
 * BHP
 *
 * (The MIT License)
 *
 * Copyright (c) 2015 Jorge Cobis <jcobis@gmail.com / http://twitter.com/cobisja>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace BHP;

use BHP\BHP;
use BHP\Helpers\Base;


class AlertBox
{
    private $html;


    public function __construct($message_or_options_with_block = null, $options = null, $block=null)
    {
      $num_args = Base::get_function_num_args(func_get_args());

      if (3 > $num_args && is_callable(func_get_arg($num_args-1))) {

          $block = func_get_arg($num_args-1);
          $this->html = $this->alert_string($this->capture_alert($block), is_null($message_or_options_with_block) || is_callable($message_or_options_with_block) ? [] : $message_or_options_with_block);
      }
      else {
          $this->html = $this->alert_string($message_or_options_with_block, is_null($options) ? [] : $options);
      }
    }

    public function __toString()
    {
        return (string) $this->html;
    }

    private function alert_string($message=null, $options=[])
    {
        $dismissible = isset($options['dismissible']);
        $context = isset($options['context']) ? $options['context'] : null;

        if ($dismissible) {
            $message = $this->add_dismiss_button_to($message);
            unset($options['dismissible']);
        }

        if (!is_null($context)) {
            unset($options['context']);
        }

        $klass = $this->alert_class($context, $dismissible);
        Base::append_class($options, $klass);

        return BHP::content_tag('div', $message, array_merge(['role'=>'alert'], $options));
    }

    private function alert_class($context=null, $dismissible=null)
    {
        $valid_contexts = ['success', 'info', 'warning', 'danger'];
        $context = Base::context_for($context, ['default'=>'info', 'valid'=>$valid_contexts]);
        $dismissible_class = $dismissible ? 'alert-dismissible' : '';

        return rtrim("alert alert-$context $dismissible_class");
    }

    private function add_dismiss_button_to($message)
    {
        $options = ['type'=>'button', 'class'=>'close', 'data-dismiss'=>'alert', 'aria-label'=>'Close'];
        $dismiss_button = BHP::content_tag('button', BHP::content_tag('span', '&times;', ['aria-hidden'=>true]), $options);

        return join('', [$dismiss_button, $message]) . "\n";
    }

    private function capture_alert(callable $block)
    {
        BHP::$alert_link = true;
        $capture = call_user_func($block);
        BHP::$alert_link = false;

        return $capture;
    }
}
