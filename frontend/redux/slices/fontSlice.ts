import { createSlice } from "@reduxjs/toolkit";

const initialState = {
    fonts: [],
    fontGroups: [],
};

export const fontSlice = createSlice({
    name: 'fontSlice',
    initialState,
    reducers: {
        setFonts: (state, action) => {
            state.fonts = action.payload;
        },
        setFontGroups: (state, action) => {
            state.fontGroups = action.payload;
        },
    },
});

export const { setFonts, setFontGroups } = fontSlice.actions;
export default fontSlice.reducer;