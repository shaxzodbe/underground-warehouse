import { configureStore } from '@reduxjs/toolkit';
import { warehouseApi } from './services/warehouseApi';

export const store = configureStore({
    reducer: {
        [warehouseApi.reducerPath]: warehouseApi.reducer,
    },
    middleware: (getDefaultMiddleware) =>
        getDefaultMiddleware().concat(warehouseApi.middleware),
});

export type RootState = ReturnType<typeof store.getState>;
export type AppDispatch = typeof store.dispatch;
