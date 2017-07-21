<?php

namespace UberRush\Resources;

class Delivery extends AbstractResource
{
    /**
     * Delivery Status Event Type
     */
    const EVENT_DELIVERY_STATUS = 'deliveries.status_changed';

    const STATUS_SCHEDULED = 'scheduled';

    const STATUS_PROCESSING = 'processing';

    const STATUS_NO_COURIERS_AVAILABLE = 'no_couriers_available';

    const STATUS_EN_ROUTE_TO_PICKUP = 'en_route_to_pickup';

    const STATUS_AT_PICKUP = 'at_pickup';

    const STATUS_EN_ROUTE_TO_STATUS_DROPOFF = 'en_route_to_dropoff';

    const STATUS__AT_DROPOFF = 'at_dropoff';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CLIENT_CANCELED = 'client_canceled';

    const STATUS_RETURNING = 'returning';

    const STATUS_RETURNED = 'returned';

    const STATUS_UNABLE_TO_RETURN = 'unable_to_return';

    const STATUS_UNABLE_TO_DELIVER = 'unable_to_deliver';

    const STATUS_UNKNOWN = 'unknown';

    /**
     * Base endpoint for Deliveries
     *
     * @var string
     */
    protected $endpoint = 'deliveries';

    /**
     * Create a new delivery
     *
     * https://developer.uber.com/docs/deliveries/references/api/v1/deliveries-get
     *
     * @param array $delivery_params
     * @return mixed
     */
    public function create(array $delivery_params = [])
    {
        return $this->setMethod('POST')->setParams($delivery_params)->send();
    }

    /**
     * Get all deliveries
     *
     * https://developer.uber.com/docs/deliveries/references/api/v1/deliveries-get
     *
     * @param array $params
     * @return mixed
     */
    public function listDeliveries($params = [])
    {
        return $this->setMethod('GET')->setParams([
            $params,
        ])->send();
    }

    /**
     * Get a delivery by id
     *
     * https://developer.uber.com/docs/deliveries/references/api/v1/deliveries-delivery_id-get
     *
     * @param string $delivery_id
     * @return mixed
     */
    public function get($delivery_id)
    {

        return $this->setEndpoint($this->getEndpoint().'/'.$delivery_id)->setMethod('GET')->send();
    }

    /**
     * Cancel a delivery
     *
     * https://developer.uber.com/docs/deliveries/references/api/v1/deliveries-delivery_id-cancel-post
     *
     * @param string $delivery_id
     * @return mixed
     */
    public function cancel($delivery_id)
    {

        return $this->setEndpoint($this->getEndpoint().'/'.$delivery_id.'/cancel')->setMethod('POST')->send();
    }
}