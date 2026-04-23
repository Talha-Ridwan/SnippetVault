import { useEffect } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';

export default function AuthCallback({ onLogin }: { onLogin: () => void }) {
    const [searchParams] = useSearchParams();
    const navigate = useNavigate();

    useEffect(() => {
        const token = searchParams.get('token');
        if (token) {
            localStorage.setItem('auth_token', token);
            onLogin();
            navigate('/dashboard', {replace:true});
        }else{
            navigate('/login?error=auth_failed', {replace:true});
        }
    }, [navigate, searchParams, onLogin]);

    return(
        <div>
            <h2>Authenticating...Please wait.</h2>
        </div>
    )
}