import Vue from 'vue';
import VueRouter from 'vue-router';

import auth from './middleware/auth';
import guest from './middleware/guest';
import index from './middleware/index';

Vue.use(VueRouter);

const router = new VueRouter({
    mode: 'history',
    base: '/',
    routes: [
        {
            path: '/',
            name: 'index',
            meta: {
                middleware: [index]
            }
        },
        {
            path: '/privacy-policy',
            name: 'privacy-policy',
            component: require('./views/PrivacyPolicy').default
        },
        {
            path: '/login',
            name: 'login',
            component: require('./views/Login').default,
            meta: {
                middleware: [guest]
            }
        },
        {
            path: '/logout',
            name: 'logout',
            component: require('./views/Logout').default,
            meta: {
                middleware: [auth]
            }
        },
        {
            path: '/calendar',
            name: 'calendar',
            component: require('./views/Calendar').default,
            meta: {
                middleware: [auth]
            }
        },
        {
            path: '/bookings',
            name: 'bookings',
            component: require('./views/Bookings').default,
            meta: {
                middleware: [auth]
            }
        },
        {
            path: '/mydata',
            name: 'mydata',
            component: require('./views/MyData').default,
            meta: {
                middleware: [auth]
            }
        },

        // Must be at the bottom
        {
            path: '*',
            component: require('./views/NotFound').default
        }
    ]
});

// Creates a `nextMiddleware()` function which not only
// runs the default `next()` callback but also triggers
// the subsequent Middleware function.
function nextFactory(context, middleware, index) {
    const subsequentMiddleware = middleware[index];
    // If no subsequent Middleware exists,
    // the default `next()` callback is returned.
    if (!subsequentMiddleware) return context.next;

    return (...parameters) => {
        // Run the default Vue Router `next()` callback first.
        context.next(...parameters);
        // Then run the subsequent Middleware with a new
        // `nextMiddleware()` callback.
        const nextMiddleware = nextFactory(context, middleware, index + 1);
        subsequentMiddleware({ ...context, next: nextMiddleware });
    };
}

router.beforeEach((to, from, next) => {
    if (to.meta.middleware) {
        const middleware = Array.isArray(to.meta.middleware)
            ? to.meta.middleware
            : [to.meta.middleware];

        const context = {
            from,
            next,
            router,
            to,
        };

        const nextMiddleware = nextFactory(context, middleware, 1);

        return middleware[0]({ ...context, next: nextMiddleware });
    }

    return next();
});

export default router;
