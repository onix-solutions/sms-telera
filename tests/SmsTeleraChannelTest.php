<?php

namespace OnixSolutions\SmsTelera\Tests;

use Mockery as M;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use OnixSolutions\SmsTelera\SmsTeleraApi;
use OnixSolutions\SmsTelera\SmsTeleraChannel;
use OnixSolutions\SmsTelera\SmsTeleraMessage;

class SmsTeleraChannelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SmsTeleraApi
     */
    private $smsc;

    /**
     * @var SmsTeleraMessage
     */
    private $message;

    /**
     * @var SmsTeleraChannel
     */
    private $channel;

    /**
     * @var \DateTime
     */
    public static $sendAt;

    public function setUp()
    {
        parent::setUp();

        $this->smsc = M::mock(SmsTeleraApi::class, [
            'login' => 'test',
            'secret' => 'test',
            'sender' => 'John_Doe',
        ]);
        $this->channel = new SmsTeleraChannel($this->smsc);
        $this->message = M::mock(SmsTeleraMessage::class);
    }

    public function tearDown()
    {
        M::close();

        parent::tearDown();
    }

    /** @test */
    public function it_can_send_a_notification()
    {
        $this->smsc->shouldReceive('send')->once()
            ->with(
                [
                    'phones'  => '+1234567890',
                    'mes'     => 'hello',
                    'sender'  => 'John_Doe',
                ]
            );

        $this->channel->send(new TestNotifiable(), new TestNotification());
    }

    /** @test */
    public function it_can_send_a_deferred_notification()
    {
        self::$sendAt = new \DateTime();

        $this->smsc->shouldReceive('send')->once()
            ->with(
                [
                    'phones'  => '+1234567890',
                    'mes'     => 'hello',
                    'sender'  => 'John_Doe',
                    'time'    => '0'.self::$sendAt->getTimestamp(),
                ]
            );

        $this->channel->send(new TestNotifiable(), new TestNotificationWithSendAt());
    }

    /** @test */
    public function it_does_not_send_a_message_when_to_missed()
    {
        $this->smsc->shouldNotReceive('send');

        $this->channel->send(
            new TestNotifiableWithoutRouteNotificationForSmsctelera(), new TestNotification()
        );
    }

    /** @test */
    public function it_can_send_a_notification_to_multiple_phones()
    {
        $this->smsc->shouldReceive('send')->once()
            ->with(
                [
                    'phones'  => '+1234567890,+0987654321,+1234554321',
                    'mes'     => 'hello',
                    'sender'  => 'John_Doe',
                ]
            );

        $this->channel->send(new TestNotifiableWithManyPhones(), new TestNotification());
    }
}

class TestNotifiable
{
    use Notifiable;

    // Laravel v5.6+ passes the notification instance here
    // So we need to add `Notification $notification` argument to check it when this project stops supporting < 5.6
    public function routeNotificationForSmsctelera()
    {
        return '+1234567890';
    }
}

class TestNotifiableWithoutRouteNotificationForSmsctelera extends TestNotifiable
{
    public function routeNotificationForSmsctelera()
    {
        return false;
    }
}

class TestNotifiableWithManyPhones extends TestNotifiable
{
    public function routeNotificationForSmsctelera()
    {
        return ['+1234567890', '+0987654321', '+1234554321'];
    }
}

class TestNotification extends Notification
{
    public function toSmsTelera()
    {
        return SmsTeleraMessage::create('hello')->from('John_Doe');
    }
}

class TestNotificationWithSendAt extends Notification
{
    public function toSmsTelera()
    {
        return SmsTeleraMessage::create('hello')
            ->from('John_Doe')
            ->sendAt(SmsTeleraChannelTest::$sendAt);
    }
}
