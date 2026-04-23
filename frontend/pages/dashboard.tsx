
import { useNavigate } from 'react-router-dom';
import { Button } from "../components/button";
import api from "../routes/api";

export default function Dashboard({ onLogout }: { onLogout: () => void }) {
    const navigate = useNavigate();

    const handleLogout = async () => {
        try {
            await api.post('/logout');
        } finally {
            localStorage.removeItem('auth_token');
            onLogout();
            navigate('/login', { replace: true });
        }
    };

    return (
        <div>
            <Button
                size={'large'}
                variant={'primary'}
                id={'logoutButton'}
                onClick={handleLogout}
            >
                Logout
            </Button>
        </div>
    );
}