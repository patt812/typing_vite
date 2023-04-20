import { shallowMount } from '@vue/test-utils';
import ForgotPassword from '@/Pages/Auth/ForgotPassword.vue';

let wrap;

jest.mock('axios', () => ({
    delete: jest.fn(() => Promise.resolve()),
}));

beforeEach(() => {
    wrap = shallowMount(ForgotPassword);
});

describe('Forgotpassword.vue', () => {
    test('can see form', () => {
        expect(wrap.vm.form.email).toBe('');
    });
});
