
import { Button } from "../components/button"; // Assuming this path is correct!
import { useSearchParams } from 'react-router-dom';

export default function LoginPage() {
    // 1. Grab the URL parameters
    const [searchParams] = useSearchParams();
    const errorMessage = searchParams.get('error');

    const handleGithubLogin = () => {
        const base = import.meta.env.VITE_API_URL ?? '';
        window.location.href = `${base}/api/auth/github/redirect`;
    };

    // 3. Render the UI
    return (
        <div style={{ padding: '2rem' }}>
            {errorMessage === 'auth_failed' && (
                <div style={{ color: 'red', marginBottom: '1rem' }}>
                    Authentication with GitHub failed. Please try again.
                </div>
            )}
            {errorMessage === 'email_required' && (
                <div style={{ color: 'red', marginBottom: '1rem' }}>
                    Your GitHub account does not have a public email address. Please add one in your GitHub settings and try again.
                </div>
            )}

            <Button
                variant='primary'
                size='medium'
                onClick={handleGithubLogin}
                id="loginButton"
            >
                Sign in or Create Account with Github
            </Button>
        </div>
    );
}