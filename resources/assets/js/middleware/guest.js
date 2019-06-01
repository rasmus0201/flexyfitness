import store from '../store';

export default function auth({ next, router }) {
    if (store.state.auth) {
        return router.push({ name: 'calendar' });
    }

    return next();
}
