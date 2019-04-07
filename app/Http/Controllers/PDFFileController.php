<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Http\Requests;

use App\User;
use App\PDF_File; //model
use App\Http\Resources\PDF_File as PDF_File_Resource; //resource
use Illuminate\Support\Facades\Storage; //deal with files
use Symfony\Component\HttpFoundation\Response;//responses

//extar layer for security
use Illuminate\Support\Facades\Auth;

class PDFFileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $user=User::where('api_token','=',$request->api_token)->firstOrFail();
        $user=auth('api')->user();
        $pdf_files = $user->pdf_files;//->paginate(15);
        // echo($pdf_files);
        return PDF_File_Resource::collection($pdf_files);
        // return $pdf_files;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //check if all parameters came
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'api_token' => 'required|exists:users', //not exactly necessary, because our API won't respond to unrecognized tokens, but interesting functionality
            'pdf_file' => 'mimetypes:application/pdf|required|max:1999',
            'pdf_password' => 'string | nullable',
        ]);
        
        //get user info
        // $user = User::where('api_token','=',$request->api_token)->firstOrFail();
        $user=auth('api')->user();
        
        //handles file upload
        if(!$validator->fails()){
            //get  filename with extension
            $filenameWithExt = $request->file('pdf_file')->getClientOriginalName();
            //get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            //get just extension
            $extension = $request->file('pdf_file')->getClientOriginalExtension();
            //filename to store
            $filenameToStore = $user->id.'_'.$filename.'_'.time().'.'.$extension;
            //upload image
            $path = $request->file('pdf_file')->storeAs('public/pdf_files',$filenameToStore);

            //now, create the DB entry.
            $file = new PDF_File;
            $file->title = $request->input('title');
            $file->filename = $filenameToStore;
            $file->user_id = $user->id;
            $file->pdf_password = $request->input('pdf_password'); //save without hashing so it's easier
            $file->save();
            return response()->json(new PDF_File_Resource($file), Response::HTTP_CREATED);
        } else {
            return $validator->errors(); //return errors
        }

    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pdf_file = PDF_File::findOrFail($id);
        //return article as resource
        return new PDF_File_Resource($pdf_file);
    }

    /**
     * Shows the pdf on the client (browser)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view(Request $request,$id)
    {
        //check if all parameters came
        $validator = Validator::make(['id'=>$id],[
            'id' => 'required|exists:pdf_files'
        ]);
        
        if(!$validator->fails()){
            $pdf_file = PDF_File::findOrFail($id);

            //return article as resource
            $user_id=auth('api')->id();
            $pass = $request->input('pdf_password','');
            if($pdf_file->user_id == $user_id && $pass==$pdf_file->pdf_password){
                return response()->file('storage/pdf_files/'.$pdf_file->filename);
            }
            return response()->json(['error'=>'unauthorized. Cannot view because probably, this isn\'t your file. Please, provide you pdf_password with the request'], 401);
        } else {
            return $validator->errors();
        }
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //check if all parameters came
        $validator = Validator::make(['id'=>$id],[
            'id' => 'required|exists:pdf_files'
        ]);
        
        //get user info
        // $user = User::where('api_token','=',$request->api_token)->firstOrFail();
        $user_id=auth('api')->id();
        // return $user_id;
        
        if(!$validator->fails()){
            //check if user is the same as from the file
            $pdf_file=PDF_File::findOrFail($id);
            //debug
            //return new PDF_File_Resource($pdf_file);
            $pass = $request->input('pdf_password','');
            if($pdf_file->user_id == $user_id && $pass==$pdf_file->pdf_password){
                //now check be sure that file exists
                if(Storage::exists('public/pdf_files/'.$pdf_file->filename)){
                    Storage::delete('public/pdf_files/'.$pdf_file->filename);
                }
                
                $pdf_file->delete();
                return response()->json(new PDF_File_Resource($pdf_file), Response::HTTP_OK);
            }
            else{
                return response()->json(['error'=>'unauthorized. Cannot delete because it\'s not your file.'], 401);;
            }
        } else {
            return $validator->errors(); //return errors
        }
    }
}
