<?php

/**
 * This code was generated by
 * ___ _ _ _ _ _    _ ____    ____ ____ _    ____ ____ _  _ ____ ____ ____ ___ __   __
 *  |  | | | | |    | |  | __ |  | |__| | __ | __ |___ |\ | |___ |__/ |__|  | |  | |__/
 *  |  |_|_| | |___ | |__|    |__| |  | |    |__] |___ | \| |___ |  \ |  |  | |__| |  \
 *
 * Twilio - Taskrouter
 * This is the public Twilio REST API.
 *
 * NOTE: This class is auto generated by OpenAPI Generator.
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */


namespace Twilio\Rest\Taskrouter\V1\Workspace;

use Twilio\Exceptions\TwilioException;
use Twilio\InstanceResource;
use Twilio\Options;
use Twilio\Values;
use Twilio\Version;
use Twilio\Deserialize;
use Twilio\Rest\Taskrouter\V1\Workspace\Task\ReservationList;


/**
 * @property string|null $accountSid
 * @property int $age
 * @property string $assignmentStatus
 * @property string|null $attributes
 * @property string|null $addons
 * @property \DateTime|null $dateCreated
 * @property \DateTime|null $dateUpdated
 * @property \DateTime|null $taskQueueEnteredDate
 * @property int $priority
 * @property string|null $reason
 * @property string|null $sid
 * @property string|null $taskQueueSid
 * @property string|null $taskQueueFriendlyName
 * @property string|null $taskChannelSid
 * @property string|null $taskChannelUniqueName
 * @property int $timeout
 * @property string|null $workflowSid
 * @property string|null $workflowFriendlyName
 * @property string|null $workspaceSid
 * @property string|null $url
 * @property array|null $links
 * @property \DateTime|null $virtualStartTime
 * @property bool|null $ignoreCapacity
 * @property string|null $routingTarget
 */
class TaskInstance extends InstanceResource
{
    protected $_reservations;

    /**
     * Initialize the TaskInstance
     *
     * @param Version $version Version that contains the resource
     * @param mixed[] $payload The response payload
     * @param string $workspaceSid The SID of the Workspace that the new Task belongs to.
     * @param string $sid The SID of the Task resource to delete.
     */
    public function __construct(Version $version, array $payload, string $workspaceSid, string $sid = null)
    {
        parent::__construct($version);

        // Marshaled Properties
        $this->properties = [
            'accountSid' => Values::array_get($payload, 'account_sid'),
            'age' => Values::array_get($payload, 'age'),
            'assignmentStatus' => Values::array_get($payload, 'assignment_status'),
            'attributes' => Values::array_get($payload, 'attributes'),
            'addons' => Values::array_get($payload, 'addons'),
            'dateCreated' => Deserialize::dateTime(Values::array_get($payload, 'date_created')),
            'dateUpdated' => Deserialize::dateTime(Values::array_get($payload, 'date_updated')),
            'taskQueueEnteredDate' => Deserialize::dateTime(Values::array_get($payload, 'task_queue_entered_date')),
            'priority' => Values::array_get($payload, 'priority'),
            'reason' => Values::array_get($payload, 'reason'),
            'sid' => Values::array_get($payload, 'sid'),
            'taskQueueSid' => Values::array_get($payload, 'task_queue_sid'),
            'taskQueueFriendlyName' => Values::array_get($payload, 'task_queue_friendly_name'),
            'taskChannelSid' => Values::array_get($payload, 'task_channel_sid'),
            'taskChannelUniqueName' => Values::array_get($payload, 'task_channel_unique_name'),
            'timeout' => Values::array_get($payload, 'timeout'),
            'workflowSid' => Values::array_get($payload, 'workflow_sid'),
            'workflowFriendlyName' => Values::array_get($payload, 'workflow_friendly_name'),
            'workspaceSid' => Values::array_get($payload, 'workspace_sid'),
            'url' => Values::array_get($payload, 'url'),
            'links' => Values::array_get($payload, 'links'),
            'virtualStartTime' => Deserialize::dateTime(Values::array_get($payload, 'virtual_start_time')),
            'ignoreCapacity' => Values::array_get($payload, 'ignore_capacity'),
            'routingTarget' => Values::array_get($payload, 'routing_target'),
        ];

        $this->solution = ['workspaceSid' => $workspaceSid, 'sid' => $sid ?: $this->properties['sid'], ];
    }

    /**
     * Generate an instance context for the instance, the context is capable of
     * performing various actions.  All instance actions are proxied to the context
     *
     * @return TaskContext Context for this TaskInstance
     */
    protected function proxy(): TaskContext
    {
        if (!$this->context) {
            $this->context = new TaskContext(
                $this->version,
                $this->solution['workspaceSid'],
                $this->solution['sid']
            );
        }

        return $this->context;
    }

    /**
     * Delete the TaskInstance
     *
     * @param array|Options $options Optional Arguments
     * @return bool True if delete succeeds, false otherwise
     * @throws TwilioException When an HTTP error occurs.
     */
    public function delete(array $options = []): bool
    {

        return $this->proxy()->delete($options);
    }

    /**
     * Fetch the TaskInstance
     *
     * @return TaskInstance Fetched TaskInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function fetch(): TaskInstance
    {

        return $this->proxy()->fetch();
    }

    /**
     * Update the TaskInstance
     *
     * @param array|Options $options Optional Arguments
     * @return TaskInstance Updated TaskInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function update(array $options = []): TaskInstance
    {

        return $this->proxy()->update($options);
    }

    /**
     * Access the reservations
     */
    protected function getReservations(): ReservationList
    {
        return $this->proxy()->reservations;
    }

    /**
     * Magic getter to access properties
     *
     * @param string $name Property to access
     * @return mixed The requested property
     * @throws TwilioException For unknown properties
     */
    public function __get(string $name)
    {
        if (\array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        }

        if (\property_exists($this, '_' . $name)) {
            $method = 'get' . \ucfirst($name);
            return $this->$method();
        }

        throw new TwilioException('Unknown property: ' . $name);
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string
    {
        $context = [];
        foreach ($this->solution as $key => $value) {
            $context[] = "$key=$value";
        }
        return '[Twilio.Taskrouter.V1.TaskInstance ' . \implode(' ', $context) . ']';
    }
}

