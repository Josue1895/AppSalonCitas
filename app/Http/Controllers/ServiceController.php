<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $servicios = Service::where('activo', 1)->get();
        return view('services.index', compact('servicios'));
    }

    public function adminIndex()
    {
        $servicios = Service::all();
        return view('admin.servicios.index', compact('servicios'));
    }

    public function create()
    {
        return view('admin.servicios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:60',
            'precio' => 'required|numeric',
            'descripcion' => 'nullable',
            'duracion' => 'required|integer',
        ]);

        Service::create([
            'nombre' => htmlspecialchars($request->nombre),
            'precio' => $request->precio,
            'descripcion' => htmlspecialchars($request->descripcion),
            'duracion' => $request->duracion,
            'activo' => 1
        ]);

        return redirect()->route('admin.servicios.index')->with('success', 'Servicio creado correctamente');
    }

    public function edit($id)
{
    $servicio = Service::findOrFail($id);
    return view('admin.servicios.edit', compact('servicio'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'nombre' => 'required|max:60',
        'precio' => 'required|numeric',
        'descripcion' => 'nullable',
        'duracion' => 'required|integer',
    ]);

    $servicio = Service::findOrFail($id);

    $servicio->update([
        'nombre' => htmlspecialchars($request->nombre),
        'precio' => $request->precio,
        'descripcion' => htmlspecialchars($request->descripcion),
        'duracion' => $request->duracion,
    ]);

    return redirect()->route('admin.servicios.index')->with('success', 'Servicio actualizado correctamente');
}

public function destroy($id)
{
    $servicio = Service::findOrFail($id);
    $servicio->delete();

    return redirect()->route('admin.servicios.index')->with('success', 'Servicio eliminado correctamente');
}



}
