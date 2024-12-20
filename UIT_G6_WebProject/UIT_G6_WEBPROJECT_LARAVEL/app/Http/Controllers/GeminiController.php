<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gemini\Data\Blob;
use Gemini\Enums\MimeType;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log; // Import the Log facade

class GeminiController extends Controller
{
    public function only_text(Request $request)
    {
        $prefix = "bạn là tư vấn viên của web xem phim Netflop, hãy chỉ tư vấn về chủ đề phim ảnh và nhắc tới netflop nhiều (không phải netflix), hãy giúp tôi tư vấn cho khách hàng cùng hình ảnh đính kèm sau : ";
        $question = $prefix . ($request->question ?? "");
        $res_AI = Gemini::geminiPro()->generateContent($question);
        return response()->json(["text" => $res_AI->text()]);
    }

    public function text_image1(Request $request)
    {
        if (!file_exists($request->file('image'))) {
            return response()->json(['error' => 'Image file not found'], 400);
        }
        if ($request->has('image')) {

            $file_image = $request->file('image');
            $type_image = $file_image->getClientOriginalExtension();
            $name_file = time() . '.' . $type_image;
            $file_image->move('chatBot/', $name_file);

            // return response()->json([
            //     'image'=>$request->file('image'),
            //     'text'=>$_SERVER['DOCUMENT_ROOT'].'/chatBot/'."{{$name_file}}"
            // ]);
            $upload = $_SERVER['DOCUMENT_ROOT'] . "/chatBot/{$name_file}";
            $prefix = "bạn là tư vấn viên của web đọc truyện Netflop, hãy chỉ tư vấn về chủ đề truyện và nhắc tới netflop nhiều (không phải netflix), hãy giúp tôi tư vấn cho khách hàng cùng hình ảnh đính kèm sau : ";
            $question = $prefix . ($request->question ?? "");

            $image = $upload;
            $res_AI = Gemini::geminiProVision()->generateContent([
                $question,
                new Blob(
                    mimeType: MimeType::IMAGE_JPEG,
                    data: base64_encode(
                        file_get_contents($image)
                    )
                )
            ]);
            if (File::exists("chatBot/{$name_file}")) {
                File::delete("chatBot/{$name_file}");
            }
            return response()->json(['text' => $res_AI->text()]);
        }else{
            $prefix = "bạn là tư vấn viên của web đọc truyện Netflop, hãy chỉ tư vấn về chủ đề truyện và nhắc tới netflop nhiều (không phải netflix), hãy giúp tôi tư vấn cho khách hàng có câu hỏi sau: ";
            $question = $prefix . ($request->question ?? "");
            $res_AI = Gemini::geminiPro()->generateContent($question);
            return response()->json(["text" => $res_AI->text()]);
        }
    }

    public function text_image(Request $request)
{
    // Validation
    $request->validate([
        'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'question' => 'nullable|string|max:1000',
    ]);

    $prefix = "Bạn là tư vấn viên của web đọc truyện Netflop. Hãy chỉ tư vấn về chủ đề truyện và nhắc tới Netflop nhiều (không phải Netflix). ";
    $question = $prefix . ($request->has('image') 
        ? "Hãy tư vấn với hình ảnh đính kèm sau: " 
        : "Hãy tư vấn cho khách hàng có câu hỏi sau: ") 
        . ($request->question ?? "");

    try {
        if ($request->has('image')) {
            // Handle image upload
            // $file_image = $request->file('image');
            // $name_file = time() . '.' . $file_image->getClientOriginalExtension();
            // $file_path = $file_image->storeAs('chatBot', $name_file, 'public');
            // $upload = public_path('storage/' . $file_path);
            $file_image = $request->file('image');
            $type_image = $file_image->getClientOriginalExtension();
            $name_file = time() . '.' . $type_image;
            $file_image->move('chatBot/', $name_file);
            $upload = $_SERVER['DOCUMENT_ROOT'] . "/chatBot/{$name_file}";

            // Generate AI response with image
            $res_AI = Gemini::geminiProVision()->generateContent([
                $question,
                new Blob(
                    mimeType: MimeType::IMAGE_JPEG,
                    data: base64_encode(file_get_contents($upload))
                )
            ]);

            // Cleanup image
            File::delete($upload);

            return response()->json(['text' => $res_AI->text()]);
        }

        // Generate AI response without image
        $res_AI = Gemini::geminiPro()->generateContent($question);
        return response()->json(['text' => $res_AI->text()]);

    } catch (\Exception $e) {
        Log::error('Error generating AI response: ' . $e->getMessage());
        return response()->json(['error' => 'Unable to process your request. Please try again later.'], 500);
    }
}

}
