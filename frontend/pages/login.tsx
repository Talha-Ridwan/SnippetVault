
import { Button } from "../components/button"; // Assuming this path is correct!
import { useSearchParams } from 'react-router-dom';

export default function LoginPage() {
    // 1. Grab the URL parameters
    const [searchParams] = useSearchParams();
    const errorMessage = searchParams.get('error');

    const handleGithubLogin = () => {
        window.location.href = 'http://localhost:8000/api/auth/github/redirect';
    };

    // 3. Render the UI
    return (
        <div style={{ padding: '2rem' }}>
            {errorMessage === 'auth_failed' && (
                <div style={{ color: 'red', marginBottom: '1rem' }}>
                    Authentication with GitHub failed. Please try again.
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