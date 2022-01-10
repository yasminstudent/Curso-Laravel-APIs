<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Todo;

class ApiController extends Controller
{
    public function create(Request $request)
    {
        $array = [ 'error' => ''];

        //Validando
        $rules = [
          'title' => 'required|min:3'
        ];
        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){ //Verifica se os campos não foram aceitos
            $array['error'] = $validator->messages();
            return $array;
        }

        $title = $request->input('title');

        //Criando o registro
        $todo = new Todo();
        $todo->title = $title;
        $todo->save();

        return $array;
    }

    public function index()
    {
        $array = [ 'error' => ''];

        $todos = Todo::paginate(2); //faz a paginação e retorna o total de itens (recebe a quantidade de itens por página)
        $todos = Todo::simplePaginate(2); //faz a paginação e não retorna o total de itens

        //$todos = Todo::where('done', 1)->simplePaginate(2); //faz a paginação com uma condição

        $array['list'] = $todos->items();
        $array['current_page'] = $todos->currentPage();
        //$array['list'] = Todo::all();

        //nesse caso tem que passar na url o parâmetro page (p/ pegar a página desejada)

        return $array;
    }

    public function show($id)
    {
        $array = [ 'error' => ''];

        $todo = Todo::find($id);

        if($todo){
            $array['item'] = $todo;
        } else{
            $array['error'] = 'A tarefa '.$id.' não existe';
        }

        return $array;
    }

    public function update(Request $request, $id)
    {
        $array = [ 'error' => ''];

        //Validando
        $rules = [
            'title' => 'min:3',
            'done' => 'boolean'
        ];
        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){ //Verifica se os campos não foram aceitos
            $array['error'] = $validator->messages();
            return $array;
        }

        $title = $request->input('title');
        $done = $request->input('done');

        //Atualizando o registro
        $todo = Todo::find($id);

        if($todo){
            if($title){
                $todo->title = $title;
            }
            if($done !== NULL){
                $todo->done = $done;
            }

            $todo->save();
        } else{
            $array['error'] = 'A tarefa '.$id.' não existe, logo não pode ser atualizada';
        }

        return $array;
    }

    public function destroy($id)
    {
        $array = [ 'error' => ''];

        $todo = Todo::find($id)->delete();

        return $array;
    }

    public function upload(Request $request)
    {
        /*
            $request->hasFile('nomeCampo'); //retorna se foi enviado um campo com o nome do parâmetro recebido e se o valor desse campo é um arquivo
            $request->file('nomeCampo')->isValid(); //retorna se o arquivo é válido (não corrompido)
            $request->file('nomeCampo')->extension(); //retorna a extensão do arquivo
        */
        $array = ['error' => ''];

        //Validação
        $rules = [
          'nome' => 'required|min:2',
          'foto' => 'required|mimes:jpg,png'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $array['error'] = $validator->messages();
            return  $array;
        }

        if($request->hasFile('foto')){
            if($request->file('foto')->isValid()){
                $foto = $request->file('foto')->store('public');

                $url = asset(Storage::url($foto)); //gera uma URL p/ acessar o arquivo baixado

                $array['url'] = $url;

                //P/ add atalho da pasta storage na pasta pública: php artisan storage:link
                //obs: ao acessar url, retirar as duas / extras que aparecem
            }
        }

        return $array;
    }
}
