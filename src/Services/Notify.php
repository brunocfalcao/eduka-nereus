<?php

namespace Eduka\Cube\Mailables;

use Eduka\Cube\Models\Course;
use Eduka\Maquillage\Mail\MaquillageMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Notify extends MaquillageMailable
{
    use Queueable, SerializesModels;

    public $view;
    public $data;
    public $model;
    public $subject;
    public $id;

    /**
     * Constructs a mailable with default data.
     *
     * @param string $view
     * @param array $data
     * @param string $model
     * @param int $id
     *
     * @return void
     */
    public function __construct(
        string $view,
        string $subject,
        array $data = [],
        string $model,
        int $id
    ) {
        $this->view = $view;
        $this->subject = $subject;
        $this->data = $data;
        $this->model = $model;
        $this->id = $id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $model = new $this->model;

        return $this->view($this->view)
                    ->subject($this->subject)
                    ->with('model', $model->find($this->id))
                    ->with('data', $this->data)
                    ->with('color', ['main' => Course::all()->first()->main_color_theme]);
    }
}
