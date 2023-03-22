<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\MinecraftLookupService;
use App\Services\SteamLookupService;
use App\Services\XblLookupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

/**
 * Class LookupController
 *
 * @package App\Http\Controllers
 */
class LookupController extends Controller
{  
    protected $minecraftLookupService;
    protected $steamLookupService;
    protected $xblLookupService;

    /**
     * @param MinecraftLookupService $minecraftLookupService
     * @param SteamLookupService $steamLookupService
     * @param XblLookupService $xblLookupService
     */
    public function __construct(
        MinecraftLookupService $minecraftLookupService, 
        SteamLookupService $steamLookupService, 
        XblLookupService $xblLookupService
    ){
        $this->minecraftLookupService = $minecraftLookupService;
        $this->steamLookupService = $steamLookupService;
        $this->xblLookupService = $xblLookupService;
    }
    public function lookup(Request $request)
    {
        try {
            $validate = $request->validate([
                'username' => 'nullable|string',
                'id' => 'nullable|string',
                'type' => 'required|string|in:minecraft,steam,xbl',
            ]);            
            $username = $validate['username'] ?? null;
            $userId = $validate['id'] ?? null;
            $type = $validate['type'];

            $cache = $type . '-' . $userId . '-' . $username;
            if (Cache::has($cache)) {
                $response = Cache::get($cache);
            } else {            
                switch ($type) {
                    case 'minecraft':
                        $result = $this->minecraftLookupService->lookup($username, $userId);
                        break;
                    case 'steam':
                        $result = $this->steamLookupService->lookup($username, $userId);
                        break;
                    case 'xbl':
                        $result = $this->xblLookupService->lookup($username, $userId);
                        break;
                    default:
                        abort(500, "Invalid type parameter");
                        break;
                }
                $response = response()->json($result);
                $status = $response->getStatusCode();

                if ($status == 200) {
                    Cache::put($cache, $result, 5 * 60);                    
                }
            }
            return $response;
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}


