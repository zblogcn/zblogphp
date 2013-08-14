using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Runtime.InteropServices;
using System.Windows.Forms;
using System.Drawing;

internal class clsAero
{
    public Form Form;
    [StructLayout(LayoutKind.Sequential)]
    public struct MARGINS
    {
        public int cxLeftWidth;
        public int cxRightWidth;
        public int cyTopHeight;
        public int cyButtomheight;
    }

    [DllImport("dwmapi.dll")]
    public static extern int DwmExtendFrameIntoClientArea(IntPtr hWnd, ref MARGINS pMarinset);


    public void Go()
    {
        Form.TransparencyKey = Color.FromArgb(255, 255, 1);
        Form.BackColor = Form.TransparencyKey;

        MARGINS margins = new MARGINS();
        margins.cxLeftWidth = -1;
        margins.cxRightWidth = -1;
        margins.cyTopHeight = -1;
        margins.cyButtomheight = -1;


        DwmExtendFrameIntoClientArea(Form.Handle, ref margins);
    }
}
