<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\App;

class AppController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('apps.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\AppRequest $request)
    {
        $app = new App();
        $app->fill($request->all());
        $app->user_id = $request->user()->id;
        $app->client_id = generate_client_id();
        $app->client_secret = generate_client_secret();
        $app->save();

        return redirect('dashboard');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $app = App::findByUserAndId($request->user(), $id);
        return view('apps.edit', ['app' => $app]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\AppRequest $request, $id)
    {
        $app = App::findOrFail($id);
        $app->fill($request->all());
        $app->save();

        return redirect('dashboard');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $app = App::findByUserAndId($request->user(), $id);
        $app->delete();
        return response('');
    }
}
