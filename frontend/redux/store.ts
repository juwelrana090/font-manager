import { configureStore } from "@reduxjs/toolkit";
import fontSlice from "./slices/fontSlice";

export const store = configureStore({
    reducer: {
        font: fontSlice
    },
});

// Types for RootState and AppDispatch
export type RootState = ReturnType<typeof store.getState>;
export type AppDispatch = typeof store.dispatch;