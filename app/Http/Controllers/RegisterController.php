<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PDOException;

class RegisterController extends Controller
{

    public function create(Request $request)
    {
        if ($request->session()->has('LoginSession')) {
            return redirect()->to('/');
        } else {
            return view('auth.registrar');
        }
    }

    public function valid(Request $request)
    {
        $user = DB::selectOne('SELECT * FROM Persona WHERE NumDoc = ? AND CIP = ?', [
            $request->dni,
            $request->cip,
        ]);
        if ($user !== null) {
            if ($user->Clave !== null) {
                return response()->json([
                    'status' => 2,
                    'message' => "Usted ya tiene una cuenta registrar, restablezca su cuenta para obtener una nueva."
                ]);
            } else {
                return response()->json([
                    'status' => 1,
                    'user' => $user,
                ]);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => "Datos no encontrados.",
            ]);
        }
    }

    public function save(Request $request)
    {
        try {
            DB::beginTransaction();
            DB::update('UPDATE Persona SET Clave = ? WHERE idDNI = ?', [
                Hash::make($request->password),
                $request->idDNI
            ]);

            DB::delete('DELETE FROM Web where idDNI = ?', [
                $request->idDNI
            ]);

            DB::insert('INSERT INTO Web(idDNI,Tipo,Direccion) VALUES(?,16,?)', [
                $request->idDNI,
                $request->email
            ]);

            DB::commit();
            return response()->json([
                'status' => 1,
                'message' => "Se guardo correctamente su contraseña, ahora puede ingresar al sistema usando su n° cip y su clave.",
            ]);
        } catch (PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 0,
                'message' => "Error de conexión, intente nuevamente en un parte de minutos.",
            ]);
        }
    }
}
