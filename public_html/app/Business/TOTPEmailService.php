<?PHP

declare(strict_types=1);

namespace app\Business;

use src\Data\Repository\TOTPEmailRepository;

class TOTPEmailService
{
    /**
     * Summary of totp
     * @var TOTPService
     */
    protected TOTPService $totp;

    /**
     * Summary of TOTPEmailRepository totpEmailRepository
     * @var TOTPEmailRepository
     */
    protected $totpEmailRepository;

    public function __construct(TOTPEmailRepository $totpEmailRepository, TOTPService $totp)
    {
        $this->totp = $totp;
        $this->totpEmailRepository = $totpEmailRepository;
    }

    /**
     * Generate a 6-digit OTP for email and save it using the repository.
     *
     * @param int $userId
     * @return string The generated OTP.
     */
    public function generateEmailOTP(int $userId): string
    {
        $otp = $this->totp->generateSecret();
        $this->totpEmailRepository->storeOTP($userId, $otp, time() + 900); // Expires in 15 minutes
        return $otp;
    }

    /**
     * Verify the provided OTP for a user and delete it upon successful authentication.
     *
     * @param int $userId
     * @param string $otp
     * @return bool True if the OTP is valid, false otherwise.
     */
    public function verifyEmailOTP(int $userId, string $otp): bool
    {
        $otpRecord = $this->totpEmailRepository->findValidOTP($userId, $otp);
        if ((bool) $otpRecord === false) {
            return false;
        }

        // $this->authenticateUser($userId);
        $this->totpEmailRepository->deleteOTP($otpRecord->id);
        return true;
    }

    /**
     * Placeholder for user authentication after successful OTP validation.
     *
     * @param int $userId
     * @return void
     */
    /*
    protected function authenticateUser(int $userId): void
    {
        // Logic for authenticating the user (e.g., setting session, token generation, etc.)
    }
    */
}
