<?php namespace Voilaah\Basecamp\Components;

use Exception;
use Db;
use Config;
use Auth;
use Flash;
use Event;
use Request;
use Redirect;
use ApplicationException;
use RainLab\Forum\Components\Topic;
use RainLab\Forum\Models\Topic as TopicModel;
use RainLab\Forum\Models\Channel as ChannelModel;
use RainLab\Forum\Models\Member as MemberModel;
use RainLab\Forum\Models\Post as PostModel;
use RainLab\Forum\Models\TopicFollow;
use RainLab\Forum\Classes\TopicTracker;

/**
 * ForumTopic Component
 */
class ForumTopic extends Topic
{

    /**
     * Add a fileuploader component dynamically to the page
     */
    public function init()
    {
        parent::init();
        $component = $this->addComponent(
            'Responsiv\Uploader\Components\FileUploader',
            'fileUploader',
            ['deferredBinding' => true]
        );

        $component->bindModel('files', new PostModel);
    }

    /**
    * @overrides
    * to run our custom save deferredbinding for fileupload function
    * create first topic first post in a channel
    */
    public function onCreate()
    {
        try {
            if (!$user = Auth::getUser()) {
                throw new ApplicationException('You should be logged in.');
            }

            $member = $this->getMember();
            $channel = $this->getChannel();

            if ($channel->is_moderated && !$member->is_moderator) {
                throw new ApplicationException('You cannot create a topic in this channel.');
            }

            if (TopicModel::checkThrottle($member)) {
                throw new ApplicationException('Please wait a few minutes before posting another topic.');
            }

            if ($member->is_banned) {
                throw new ApplicationException('You cannot create new topics: Your account is banned.');
            }

            // EDIT CODE
            // $topic = TopicModel::createInChannel($channel, $member, post());
            trace_log('before');
            $topic = static::createInChannel($channel, $member, post());
            trace_log('after');
            // END EDIT CODE
            $topicUrl = $this->currentPageUrl([$this->paramName('slug') => $topic->slug]);

            Flash::success(post('flash', 'Topic created successfully!'));

            /*
             * Extensbility
             */
            Event::fire('rainlab.forum.topic.create', [$this, $topic, $topicUrl]);
            $this->fireEvent('topic.create', [$topic, $topicUrl]);

            /*
             * Redirect to the intended page after successful update
             */
            $redirectUrl = post('redirect', $topicUrl);

            return Redirect::to($redirectUrl);
        }
        catch (Exception $ex) {
            Flash::error($ex->getMessage());
        }
    }

    /**
    * @overrides
    * to run our custom save deferredbinding for fileupload function
    * create new post in a existing topic in a channel
    */
    public function onPost()
    {
        try {
            if (!$user = Auth::getUser()) {
                throw new ApplicationException('You should be logged in.');
            }

            $member = $this->getMember();
            $topic = $this->getTopic();

            if (!$topic || !$topic->canPost()) {
                throw new ApplicationException('You cannot edit posts or make replies.');
            }

            // EDIT CODE
            // $post = PostModel::createInTopic($topic, $member, post());
            $post = static::createInTopic($topic, $member, post());
            // END EDIT CODE
            $postUrl = $this->currentPageUrl([$this->paramName('slug') => $topic->slug]);

            TopicFollow::sendNotifications($topic, $post, $postUrl);
            Flash::success(post('flash', 'Response added successfully!'));

            /*
             * Extensbility
             */
            Event::fire('rainlab.forum.topic.post', [$this, $post, $postUrl]);
            $this->fireEvent('topic.post', [$post, $postUrl]);

            /*
             * Redirect to the intended page after successful update
             */
            $redirectUrl = post('redirect', $postUrl);

            return Redirect::to($redirectUrl.'?page=last#post-'.$post->id);
        }
        catch (Exception $ex) {
            Flash::error($ex->getMessage());
        }
    }


    /**
     * @overrides from Rain;ab\Forum\Models\Topic
     * Creates a topic and a post inside a channel
     * @param  Channel $channel
     * @param  Member $member
     * @param  array $data Topic and post data: subject, content.
     * @return self
     */
    public static function createInChannel($channel, $member, $data)
    {
        $topic = new TopicModel;
        $topic->subject = array_get($data, 'subject');
        $topic->channel = $channel;
        $topic->start_member = $member;

        $post = new PostModel;
        $post->topic = $topic;
        $post->member = $member;
        $post->content = array_get($data, 'content');

        Db::transaction(function() use ($topic, $post, $data) {
            $topic->save();
            // EDIT CODE
            $post->save(null, $data['_session_key']);
            // $post->save();
            // END EDIT CODE
        });

        TopicFollow::follow($topic, $member);
        $member->touchActivity();

        return $topic;
    }

    /**
     * @overrides Rainlab\Forum\Model\Post
     * Creates a post inside a topic
     * @param  Topic $topic
     * @param  Member $member
     * @param  array $data Post data: subject, content.
     * @return self
     */
    public static function createInTopic($topic, $member, $data)
    {
        $post = new PostModel;
        $post->topic = $topic;
        $post->member = $member;
        $post->subject = array_get($data, 'subject', $topic->subject);
        $post->content = array_get($data, 'content');
        // EDIT CODE
        $post->save(null, $data['_session_key']);
        // $post->save();
        // END EDIT CODE

        TopicFollow::follow($topic, $member);
        $member->touchActivity();

        return $post;
    }
}
