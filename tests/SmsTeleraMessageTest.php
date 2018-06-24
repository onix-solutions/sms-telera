<?php

namespace OnixSolutions\SmsTelera\Test;

use OnixSolutions\SmsTelera\SmsTeleraMessage;

class SmsTeleraMessageTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_can_accept_a_content_when_constructing_a_message()
    {
        $message = new SmsTeleraMessage('hello');

        $this->assertEquals('hello', $message->content);
    }

    /** @test */
    public function it_can_accept_a_content_when_creating_a_message()
    {
        $message = SmsTeleraMessage::create('hello');

        $this->assertEquals('hello', $message->content);
    }

    /** @test */
    public function it_can_set_the_content()
    {
        $message = (new SmsTeleraMessage())->content('hello');

        $this->assertEquals('hello', $message->content);
    }

    /** @test */
    public function it_can_set_the_from()
    {
        $message = (new SmsTeleraMessage())->from('John_Doe');

        $this->assertEquals('John_Doe', $message->from);
    }

    /** @test */
    public function it_can_set_the_send_at()
    {
        $sendAt = date_create();
        $message = (new SmsTeleraMessage())->sendAt($sendAt);

        $this->assertEquals($sendAt, $message->sendAt);
    }
}
