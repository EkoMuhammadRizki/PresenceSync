using System;
using System.Diagnostics;
using System.Runtime.InteropServices;
using System.Windows.Forms;

namespace SiapPresenceSync
{
    static class Program
    {
        [STAThread]
        static void Main()
        {
            try
            {
                string url = "https://siapsman1ciparay.test/";
                
                // Buka browser default ke URL PresenceSync
                ProcessStartInfo psi = new ProcessStartInfo
                {
                    FileName = url,
                    UseShellExecute = true
                };
                Process.Start(psi);
            }
            catch (Exception ex)
            {
                MessageBox.Show("Gagal membuka browser: " + ex.Message, "SIAP PresenceSync", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }
    }
}
